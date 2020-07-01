<?php

namespace DefaultBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatistiquePdfControllerTest extends WebTestCase
{
    public function testDossierdeposerbyperiodepdf()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'liste-dossiers-deposer-by-periode-en-pdf');
    }

    public function testDossierquittancerornotbyperiodepdf()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'liste-dossiers-quittancer-or-not-by-pariode-en-pdf');
    }

}
