<?php

namespace adamcameron\symfonythefasttrack\tests\Functional\Controller\ConferenceController\Index;

use adamcameron\symfonythefasttrack\Controller\ConferenceController;
use adamcameron\symfonythefasttrack\Entity\Conference;
use adamcameron\symfonythefasttrack\Repository\ConferenceRepository;
use adamcameron\symfonythefasttrack\tests\Fixture\DomDoc;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;

/** @testdox Tests for the ConferenceController's index method */
class Test extends KernelTestCase
{
    /** @testdox /conference includes well-formed links to each conference */
    public function testIndexConferenceLinks()
    {
        $conferenceRepository = $this->getConferenceRepository();

        $controller = new ConferenceController();
        $container = self::getContainer();
        $controller->setContainer($container);

        $result = $controller->index($conferenceRepository);

        $this->assertResponseIsCorrect($result, $conferenceRepository);
    }

    private function getConferenceRepository(): ConferenceRepository
    {
        $conferenceRepository = $this->getMockBuilder(ConferenceRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $conferenceCount = 3;
        $conferencesToTestFor = range(1, $conferenceCount);
        $testConferences = array_map(function () {
            $conferenceId = rand(1, 100);
            $conference = $this->getMockBuilder(Conference::class)
                ->disableOriginalConstructor()
                ->getMock();
            $conference
                ->method("getId")
                ->willReturn($conferenceId);
            $conference
                ->method("getCity")
                ->willReturn("CITY" . $conferenceId);
            $conference
                ->method("getYear")
                ->willReturn("YEAR" . $conferenceId);
            return $conference;
        }, $conferencesToTestFor);
        $conferenceRepository
            ->method("findAll")
            ->willReturn($testConferences);
        return $conferenceRepository;
    }

    private function assertResponseIsCorrect(Response $result, ConferenceRepository $conferenceRepository): void
    {
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());

        $xpathDocument = DomDoc::getContentAsXpathDocument($result);
        $links = $xpathDocument->query("//h4/following-sibling::p/a");

        $conferences = $conferenceRepository->findAll();
        $this->assertEquals(count($conferences), count($links));
        foreach ($links as $i => $link) {
            $this->assertStringContainsString("/conference/" . $conferences[$i], $link->getAttribute("href"));
        }
    }
}
