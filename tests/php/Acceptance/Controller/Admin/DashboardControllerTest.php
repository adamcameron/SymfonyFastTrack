<?php

namespace adamcameron\symfonythefasttrack\tests\Acceptance\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/** @testdox Tests the endpoints in the DashboardController */
class DashboardControllerTest extends WebTestCase
{
    /** @testdox The index endpoint redirects to the conference CRUD page */
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/');

        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }
}
