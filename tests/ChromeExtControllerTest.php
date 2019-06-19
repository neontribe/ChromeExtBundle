<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KimaiPlugin\ChromeExtBundle\tests;

use App\Entity\User;
use App\Entity\Customer;
use App\Tests\Controller\ControllerBaseTest;
use KimaiPlugin\ChromeExtBundle\Entity\ExtProject;
use App\Entity\Project;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use KimaiPlugin\ChromeExtBundle\Controller\ChromeExtController;

/**
 *
 * @coversDefaultClass \App\Controller\AboutController
 * @group integration
 */
class ChromeExtControllerTest extends ControllerBaseTest
{
    public function testIndexAction()
    {
        $client = $this->getClientForAuthenticatedUser();
        $this->request($client, '/chrome-ext/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());

        $result = $client->getCrawler()->filter('li.customername');
        $this->assertEquals(1, count($result));
        echo "\n\n" . $result->html() . "\n\n";

        $result = $client->getCrawler()->filter('li.project');
        $this->assertEquals(1, count($result));

//         $button = $client->getCrawler()
//             ->filter('li.project a')
//             ->eq(0)
//             ->link();
//         $project_page = $client->click($button);
//         echo ($project_page->html());
    }
}
