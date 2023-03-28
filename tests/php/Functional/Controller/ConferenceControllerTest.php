<?php

namespace adamcameron\symfonythefasttrack\tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/** @testdox Tests the functionality of ConferenceController */
class ConferenceControllerTest extends WebTestCase
{
    /** @testdox The index action returns a successful response */
    public function testIndex()
    {
        $client = static::createClient();
        $client->request("GET", "/conference/");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains("h1", "Hello ConferenceController!");
    }
}
