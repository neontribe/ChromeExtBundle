<?php
namespace KimaiPlugin\ChromeExtBundle\Controller;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 *
 * @Route(path="/admin/chrome-ext")
 * @Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('edit-chrome-ext')")
 */
class ChromeExtAdminController extends AbstractController {

  /**
   *
   * @Route(path="", name="chrome_ext_admin", methods={"GET", "POST"})
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function indexAction(Request $request) {
    return $this->render('@ChromeExt/index.html.twig');
  }
}
