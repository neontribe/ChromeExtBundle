<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\ChromeExtBundle\Tests;

use App\Entity\User;
use App\Tests\Controller\ControllerBaseTest;


/**
 * @coversDefaultClass \App\Controller\AboutController
 * @group integration
 */
class ChromeExtControllerTest extends ControllerBaseTest
{
    public function testIndexAction()
    {
        $client = $this->getClientForAuthenticatedUser();
        $this->request($client, '/chrome-ext/');
        // $this->assertTrue($client->getResponse()->isSuccessful());
        echo "\n*** ";
        print_r($client->getCrawler()->filter('li.customername'));
        echo "\n";

        $result = $client->getCrawler()->filter('li.customername');
        $this->assertEquals(1, count($result));

//          $result = $client->getCrawler()->filter('div.box-body pre');
//          $this->assertEquals(1, count($result));
//          $this->assertContains('MIT License', $result->text());
    }

}
