<?php

namespace adamcameron\symfonythefasttrack\tests\Functional\Controller\ConferenceController\Show;

use adamcameron\symfonythefasttrack\Controller\ConferenceController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

/** @testdox Tests for the ConferenceController show method */
class Test extends KernelTestCase
{
    use Dependencies;
    use Assertions;

    /** @testdox /conference/{id} displays correct conference and comments */
    public function testShow()
    {
        $request = new Request();
        $conferenceTitle = "MOCKED_CONFERENCE";
        list($conference, $commentRepository) = $this->getTestDependencies($conferenceTitle);

        $controller = new ConferenceController();
        $container = self::getContainer();
        $controller->setContainer($container);

        $result = $controller->show($request, $conference, $commentRepository);

        $this->assertResponseIsCorrect($result, $conference, $commentRepository);
    }
}
