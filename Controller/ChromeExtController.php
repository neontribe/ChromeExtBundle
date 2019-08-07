<?php
namespace KimaiPlugin\ChromeExtBundle\Controller;

// use App\Controller\AbstractController;
use App\Entity\Project;
use App\Entity\ProjectMeta;
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
            if ($project->getId() == 1) dump($project);
        }

        return $this->render('@ChromeExt/projects.html.twig', [
            'customers' => $_customers,
            'projects' => $_projects,
            'path' => $url = $this->generateUrl(
                'neontribe_ext_project_update',
                 [
                    'project' => 'PROJECT_ID',
                    'extid' => 'EXT_ID',
                     ]
                 ),
        ]);
    }

    /**
     * Returns a list of projects.
     *
     * , methods="POST")
     * 
     * @Route(path="/project/{project}/update", name="neontribe_ext_project_update")
     * @Security("is_granted('create_own_timesheet')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function projectUpdateAction(Request $request, Project $project)
    {
        $value = $request->query->get("extid");
        if ($value != null) {
            $externalId = (new ProjectMeta())->setName('externalID')->setValue($value);
            $project->setMetaField($externalId);
            $this->getDoctrine()->getManager()->persist($project);
            $this->getDoctrine()->getManager()->flush();
        }
        return $this->projectAction($request);
    }
}