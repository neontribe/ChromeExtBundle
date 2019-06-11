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
 * @Route(path="/chrome-ext")
 */
class ChromeExtController extends AbstractController implements CalculatorInterface {

  /**
   * Returns a list of projects.
   *
   * @Route(path="/", name="chrome_ext_index")
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function indexAction(Request $request) {
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
   * @Route(path="/{projectId}", name="chrome_ext_project")
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function projectAction(Request $request, $projectId) {
    $entityManager = $this->getDoctrine()->getManager();
    $kimaiprojectRepo = $entityManager->getRepository(Project::class);
    $projectRepo = $entityManager->getRepository(ExtProject::class);
    $issueRepo = $entityManager->getRepository(ExtIssue::class);

    // Get the kimai project bu id
    $kimiaProject = $kimaiprojectRepo->find($projectId);

    // project param is a uuid, look up the kimai project
    /**
     *
     * @var ExtProject $extProject
     */
    $extProject = $projectRepo->findOneBy([
      'project' => $kimiaProject
    ]);

    $issues = [];
    if ($extProject) {
        $issues = $extProject->getIssues();
    }

    return $this->render('@ChromeExt/issues.html.twig', [
      'project' => $kimiaProject,
      'extproject' => $extProject,
      'issues' => $issues,
    ]);
  }

  /**
   *
   * @Route(path="/{project}/{issue}", name="chrome_ext_list")
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function listAction(Request $request, $project, $issue) {
    $entityManager = $this->getDoctrine()->getManager();
    $projectRepo = $entityManager->getRepository(ExtProject::class);
    $issueRepo = $entityManager->getRepository(ExtIssue::class);

    /*
     * project param is a uuid, look up the kimai project
     * @var ExtProject
     */
    $extProject = $projectRepo->findOneBy([
      'uuid' => $project
    ]);

    if (! $extProject) {
      // if there is no project then redirect to project association.
      return $this->redirectToRoute('chrome_ext_edit_project', [
        'project' => $project,
        'issue' => $issue
      ]);
    }

    // Now fetch all timesheets for the issue
    /*
     * var KimaiPlugin\ChromeExtBundle\Entity\ExtIssue
     */
    $extIssue = $issueRepo->findOneBy([
      'uuid' => $issue
    ]);
    if ($extIssue) {
      $timesheets = $extIssue->getTimesheets();
    } else {
      // Create the new ext issue
      $extIssue = new ExtIssue();
      $extIssue->setUuid($issue);
      $extIssue->setProject($extProject);
      $entityManager->persist($extIssue);
      $timesheets = [];
    }

    foreach ($timesheets as $timesheet) {
      error_log($timesheet->getDescription());
    }
    return $this->render('@ChromeExt/list.html.twig', [
      'project' => $project,
      'projectId' => $extProject->getProject()
        ->getId(),
      'issue' => $issue,
      'timesheets' => $timesheets
    ]);
  }

  /**
   *
   * @Route(path="/{project}/{issue}/project", name="chrome_ext_edit_project", methods={"GET", "POST"})
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function projectEditAction(Request $request, $project, $issue) {
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
        'project' => $kimaiProject->getUuid(),
        'issue' => $issue
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
   * @Route(path="/{project}/{issue}/{id}", name="chrome_ext_delete", methods={"DELETE"})
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function deleteAction(Request $request, $project, $issue, $id) {
    return $this->render('@ChromeExt/list.html.twig', [
      'project' => $project,
      'issue' => $issue
    ]);
  }

public function calculate(Timesheet $record)
    {
    error_log("HERE£");
}

}
