<?php

namespace DefaultBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class makeExcelControllerControllerTest extends WebTestCase
{
    public function testStatdepot()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/statDepot');
    }

    public function testStatcaisse()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/statCaisse');
    }

    public function testStatsaisi()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/statSaisi');
    }

    public function testStatgreffe()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/statGreffe');
    }

    public function testStatretrait()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/statRetrait');
    }

    public function testStatglobalecircuit()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/StatGlobaleCircuit');
    }

}
