<?php

/*
 * This file is part of the Kimai CustomCSSBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KimaiPlugin\ChromeExtBundle\Controller;

use App\Controller\AbstractController;
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
   * @Route(path="/{project}/{issue}", name="chrome_ext", methods={"GET", "POST"})
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function indexAction(Request $request, $project, $issue) {
    return $this->render('@ChromeExt/index.html.twig');
  }
}
