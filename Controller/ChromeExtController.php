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
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 *
 * @Security("is_granted('view_own_timesheet')")
 * @Route(path="/neontribe/ext")
 */
class ChromeExtController extends AbstractController
{
    /**
     * Returns a list of settings.
     *
     * @Route(path="/settings", name="neontribe_ext_settigs")
     * @Security("is_granted('create_own_timesheet')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $settings = [
            'duration_only' => true,
            'show_tags' => true,
            'fixed_rate' => false,
            'hourl_yrate' => false,
        ];

        $response = new JsonResponse($settings);
        return $response;
    }

    /**
     * Returns a list of projects.
     *
     * @Route(path="/projects", name="neontribe_ext_projects", methods="GET")
     * @Security("is_granted('create_own_timesheet')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function projectAction(Request $request)
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

        return $this->render('@ChromeExt/projects.html.twig', [
            'customers' => $_customers,
            'projects' => $_projects
        ]);
    }

    /**
     * Returns a list of projects.
     *
     * @Route(path="/project/{id}/update", name="neontribe_ext_project_update", methods="POST")
     * @Security("is_granted('create_own_timesheet')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function projectUpdateAction(Request $request, Project project)
    {
        return $this->projectAction($request);
    }
}