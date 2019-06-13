<?php
namespace KimaiPlugin\ChromeExtBundle\Controller;

// use App\Controller\AbstractController;
use App\Entity\Project;
use App\Entity\Timesheet;
use App\Form\TimesheetEditForm;
use App\Timesheet\CalculatorInterface;
use KimaiPlugin\ChromeExtBundle\Entity\ExtIssue;
use KimaiPlugin\ChromeExtBundle\Entity\ExtProject;
use KimaiPlugin\ChromeExtBundle\Form\ExtProjectType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 *
 * @Security("is_granted('view_own_timesheet')")
 * @Route(path="/chrome-ext")
 */
class ChromeExtController extends AbstractController
{

    /**
     * Returns a list of projects.
     *
     * @Route(path="/", name="chrome_ext_index")
     * @Security("is_granted('create_own_timesheet')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $projectRepo = $entityManager->getRepository(Project::class);

        $projects = $projectRepo->findAll();

        $_customers = [];
        $_projects = [];

        foreach ($projects as $project) {
            $customer = $project->getCustomer();
            $customerName = $customer->getName();
            if (! array_key_exists($customerName, $_customers)) {
                $_customers[$customerName] = $customer;
                $_projects[$customerName] = [];
            }
            $_projects[$customerName][] = $project;
        }

        return $this->render('@ChromeExt/index.html.twig', [
            'customers' => $_customers,
            'projects' => $_projects
        ]);
    }

    /**
     * Returns a list of issues in a project.
     *
     * @Route(path="/{project}", name="chrome_ext_project")
     * @Security("is_granted('create_own_timesheet')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function projectAction(Project $project, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $kimaiprojectRepo = $entityManager->getRepository(Project::class);
        $projectRepo = $entityManager->getRepository(ExtProject::class);

        // Get the kimai project bu id
        $project = $kimaiprojectRepo->find($projectId);

        // project param is a uuid, look up the kimai project
        /**
         *
         * @var ExtProject $extProject
         */
        $extProject = $projectRepo->findOneBy([
            'project' => $project
        ]);

        $issues = [];
        if ($extProject) {
            $issues = $extProject->getIssues();
        }

        return $this->render('@ChromeExt/issues.html.twig', [
            'project' => $project,
            'extproject' => $extProject,
            'issues' => $issues
        ]);
    }

    /**
     * This is the desired entry point for the iframe.
     * @Security("is_granted('create_own_timesheet')")
     *
     * @Route(path="/{projectUuid}/{issueUuid}", name="chrome_ext_list")
     *
     * @param string $projectUuid
     *            The unique identifier for the project from the ticket or issue.
     * @param string $issueUuid
     *            The unique identifier for the issue from the ticket or issue.
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction($projectUuid, $issueUuid, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $projectRepo = $entityManager->getRepository(ExtProject::class);
        $issueRepo = $entityManager->getRepository(ExtIssue::class);

        /*
         * The project param is a uuid, look up the real kimai project
         * @var ExtProject
         */
        $extProject = $projectRepo->findOneBy([
            'uuid' => $projectUuid
        ]);

        if (! $extProject) {
            // If there is no project then redirect to project association.
            return $this->redirectToRoute('chrome_ext_edit_project', [
                'project' => $projectUuid,
                'issue' => $issueUuid
            ]);
        }

        /*
         * Load the issue bridge entity identified by that UUID, this will have alist of timesheets associated with it.
         *
         * var KimaiPlugin\ChromeExtBundle\Entity\ExtIssue
         */
        $extIssue = $issueRepo->findOneBy([
            'uuid' => $issueUuid
        ]);
        if ($extIssue) {
            // Fetch all timesheets associated with this issue.
            $timesheets = $extIssue->getTimesheets();
        } else {
            // No issue, create a new issue bridge
            $extIssue = new ExtIssue();
            $extIssue->setUuid($issueUuid);
            $extIssue->setProject($extProject);
            $entityManager->persist($extIssue);
            $entityManager->flush();
            $timesheets = [];
        }

        return $this->render('@ChromeExt/list.html.twig', [
            'extProject' => $extProject,
            'extIssue' => $extIssue,
            'timesheets' => $timesheets
        ]);
    }

    /**
     *
     * @Route(path="/{project}/{issue}/project", name="chrome_ext_edit_project", methods={"GET", "POST"})
     * @Security("is_granted('create_own_timesheet')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function projectEditAction(Request $request, $project, $issue)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repo = $entityManager->getRepository(ExtProject::class);

        // project param is a uuid, look up the kimai project
        /**
         *
         * @var KimaiPlugin\ChromeExtBundle\Entity\ExtProject
         */
        $kimaiProject = $repo->findOneBy([
            'uuid' => $project
        ]);

        $form = $this->createForm(ExtProjectType::class, null, [
            'kimai_project' => $kimaiProject,
            'project_repo' => $this->getDoctrine()
                ->getManager()
                ->getRepository(Project::class)
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();
            $_project = $task['project'];

            if (! $kimaiProject) {
                $kimaiProject = new ExtProject();
                $kimaiProject->setUuid($project);
            }
            $kimaiProject->setProject($_project);

            $entityManager->persist($kimaiProject);
            $entityManager->flush();

            return $this->redirectToRoute('chrome_ext_list', [
                'projectUuid' => $kimaiProject->getUuid(),
                'issueUuid' => $issue
            ]);
        }

        return $this->render('@ChromeExt/project.html.twig', [
            'projectUuid' => $project,
            'kimai_project' => $kimaiProject,
            'form' => $form->createView()
        ]);
    }

    /**
     *
     * @Route(path="/{timesheet}/{issue}/delete", name="chrome_ext_delete")
     * @Security("is_granted('delete_other_timesheet')")
     *
     * @param Request $request
     * @param Timesheet $timesheet
     * @param ExtIssue $issue
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Timesheet $timesheet, ExtIssue $issue, Request $request)
    {
        return $this->render('@ChromeExt/delete.html.twig', [
            'timesheet' => $timesheet,
            'project' => $issue->getProject(),
            'issue' => $issue
        ]);
    }

    /**
     *
     * @Route(path="/{timesheet}/{extIssue}/delete/impl", name="chrome_ext_delete_impl")
     * @Security("is_granted('delete_other_timesheet')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteImplAction(Timesheet $timesheet, ExtIssue $extIssue, Request $request)
    {
        if (! $extIssue) {
            throw $this->createNotFoundException('Issue not found');
        }
        if (! $timesheet) {
            throw $this->createNotFoundException('Timesheet not found');
        }

        $checked = $request->query->get('checked');

        $entityManager = $this->getDoctrine()->getManager();

        $extIssue->removeTimesheet($timesheet);
        $entityManager->persist($extIssue);
        $entityManager->flush();

        // Can't cascade as we don't own the timesheet object.
        $entityManager->remove($timesheet);
        $entityManager->flush();

        return $this->redirectToRoute("chrome_ext_list",[
            "projectUuid" => $extIssue->getProject()->getUuid(),
            "issueUuid" => $extIssue->getUuid()
        ]);
    }

}
