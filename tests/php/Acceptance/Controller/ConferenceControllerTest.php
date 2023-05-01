<?php

namespace adamcameron\symfonythefasttrack\tests\Acceptance\Controller;

use adamcameron\symfonythefasttrack\Entity\Conference;
use adamcameron\symfonythefasttrack\tests\Fixture\Container;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/** @testdox Tests for the ConferenceController */
class ConferenceControllerTest extends WebTestCase
{
    /** @testdox The index endpoint returns a 200 response */
    public function testIndex()
    {
        $client = static::createClient();
        $client->request("GET", "/conference/");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains("h2", "Give your feedback!");
    }

    /** @testdox /conference/{id} end point returns a 200 OK for a valid conference */
    public function testShow()
    {
        $testConference = $this->getConferenceToTestWith();

        $client = static::createClient();
        $client->request("GET", "/conference/" . $testConference->getId());

        $this->assertResponseIsForCorrectConference($testConference);
    }

    private function assertResponseIsForCorrectConference(Conference $testConference): void
    {
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            "title",
            sprintf(
                "Conference Guestbook - %s %s",
                $testConference->getCity(),
                $testConference->getYear()
            )
        );
    }

    /** @testdox /conference/{id} end point returns a 404 if the conference is not found */
    public function testShowNotFound()
    {
        $client = static::createClient();
        $client->request("GET", "/conference/999999");

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /** @testdox /conference/{id} also displays any comment associated with the conference */
    public function testShowComments()
    {
        $testConference = $this->getConferenceToTestWith();

        $client = static::createClient();
        $client->request("GET", "/conference/" . $testConference->getId());

        $this->assertResponseIsSuccessful();
        $this->assertCommentersAreCorrect($client, $testConference);
    }

    private function getConferenceToTestWith(): Conference
    {
        $container = Container::getContainer();

        $repo = $container->get("testing.ConferenceRepository");
        $qb = $repo->createQueryBuilder("cf");

        $qb->select("cf")
            ->setMaxResults(1)
            ->leftJoin("cf.comments", "cm")
            ->groupBy("cf.id")
            ->having("count(cm.id) > 1");
        $testConference = $qb->getQuery()->getResult();

        if (!$testConference) {
            $this->markTestSkipped("No suitable conferences in database, test aborted");
        }

        return $testConference[0];
    }

    private function assertCommentersAreCorrect(KernelBrowser $client, Conference $testConference): void
    {
        $commenters = $client
            ->getCrawler()
            ->filter("body > h4")
            ->each(function ($node) {
                return $node->text();
            });

        $expectedCommenters = $this->getExpectedCommenters($testConference);
        $this->assertEquals($expectedCommenters, $commenters);
    }

    private function getExpectedCommenters(Conference $testConference): array
    {
        $expectedCommenters = $testConference->getComments()->toArray();
        usort($expectedCommenters, function ($e1, $e2) {
            return $e2->getCreatedAt() <=> $e1->getCreatedAt();
        });
        $expectedCommenters = array_slice($expectedCommenters, 0, 2);
        $expectedCommenters = array_map(function ($comment) {
            return $comment->getAuthor();
        }, $expectedCommenters);
        return $expectedCommenters;
    }
}
