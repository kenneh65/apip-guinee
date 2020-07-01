<?php

namespace DefaultBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PayCardControllerControllerTest extends WebTestCase
{
    public function testMakepaiement()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'makePaiementPayCard');
    }

    public function testReturning()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'returning-after-paycard-paiement');
    }

}
