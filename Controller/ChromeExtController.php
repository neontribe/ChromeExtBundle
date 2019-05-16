<?php

/*
 * This file is part of the Kimai CustomCSSBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KimaiPlugin\ChromeExtBundle\Controller;

// use App\Controller\AbstractController;
use App\Entity\Project;
use App\Entity\Timesheet;
use App\Form\TimesheetEditForm;
use KimaiPlugin\ChromeExtBundle\Entity\ExtIssue;
use KimaiPlugin\ChromeExtBundle\Entity\ExtProject;
use KimaiPlugin\ChromeExtBundle\Form\ExtProjectType;
use KimaiPlugin\ChromeExtBundle\Form\ExtTimesheetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 *
 * @Route(path="/chrome-ext")
 */
class ChromeExtController extends AbstractController {

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

    // project param is a uuid, look up the kimai project
    $extProject = $projectRepo->findOneBy([
      'uuid' => $project
    ]);

    if (! $extProject) {
      // if there is no project then redirect to project association.
      return $this->redirectToRoute('chrome_ext_project', [
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

    $timesheetId = $request->query->get('timesheet_id');
    if ($timesheetId) {
      $timesheet = $this->getDoctrine()
        ->getManager()
        ->getRepository(Timesheet::class)
        ->find($timesheetId);
    } else {
      $timesheet = new Timesheet();
      $user = $this->container->get('security.token_storage')
        ->getToken()
        ->getUser();
      $timesheet->setUser($user);
    }

    $form = $this->createForm(TimesheetEditForm::class, $timesheet);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      /**
       *
       * @var App\Entity\Timesheet
       */
      $timesheet = $form->getData();
      $entityManager->persist($timesheet);

      $extIssue->addTimesheet($timesheet);
      $entityManager->persist($extIssue);
      $entityManager->flush();

      return $this->redirectToRoute('chrome_ext_list', [
        'project' => $extProject->getUuid(),
        'issue' => $issue
      ]);
    }

    error_log(count($timesheets));
    foreach ($timesheets as $timesheet) {
      error_log($timesheet->getDescription());
    }
    return $this->render('@ChromeExt/index.html.twig', [
      'project' => $project,
      'issue' => $issue,
      'timesheets' => $timesheets,
      'form' => $form->createView()
    ]);
  }

  /**
   *
   * @Route(path="/{project}/{issue}/project", name="chrome_ext_project", methods={"GET", "POST"})
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function projectAction(Request $request, $project, $issue) {
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
    return $this->render('@ChromeExt/index.html.twig', [
      'project' => $project,
      'issue' => $issue
    ]);
  }
}
