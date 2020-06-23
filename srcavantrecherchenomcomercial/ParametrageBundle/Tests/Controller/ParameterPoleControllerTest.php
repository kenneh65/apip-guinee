<?php

namespace ParametrageBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParameterPoleControllerTest extends WebTestCase
{
    public function testAjouterpole()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/ajouterPole');
    }

}
