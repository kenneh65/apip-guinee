<?php

namespace DefaultBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationControllerTest extends WebTestCase
{
    public function testReservation()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/reservation');
    }

}
