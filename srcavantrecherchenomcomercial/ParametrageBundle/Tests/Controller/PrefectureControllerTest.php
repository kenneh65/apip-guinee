<?php

namespace ParametrageBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrefectureControllerTest extends WebTestCase
{
    public function testListerprefecture()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/listerPrefecture');
    }

    public function testAjoutprefecture()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/ajoutPrefecture');
    }

}
