<?php

namespace adamcameron\symfonythefasttrack\tests\Functional\Controller\ConferenceController\Show;

use adamcameron\symfonythefasttrack\Entity\Conference;
use adamcameron\symfonythefasttrack\Repository\CommentRepository;
use adamcameron\symfonythefasttrack\tests\Fixture\DomDoc;
use DOMXPath;
use Symfony\Component\HttpFoundation\Response;

trait Assertions
{

    private function assertResponseIsCorrect(Response $result, Conference $conference, mixed $commentRepository): void
    {
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());

        $xpathDocument = DomDoc::getContentAsXpathDocument($result);
        $this->assertTitleIsCorrect($xpathDocument);
        $this->assertSubheadingIsCorrect($xpathDocument, $conference);
        $this->assertCommentsAreCorrect($conference, $xpathDocument, $commentRepository);
    }

    private function assertTitleIsCorrect(DOMXPath $xpathDocument): void
    {
        $title = $xpathDocument->query('/html/head/title[text()]');
        $this->assertCount(1, $title);
        $this->assertEquals("Conference Guestbook - MOCKED_CONFERENCE", $title->item(0)->textContent);
    }

    private function assertSubheadingIsCorrect(DOMXPath $xpathDocument, string $conferenceTitle): void
    {
        $h2 = $xpathDocument->query('/html/body/h2[text()]');
        $this->assertCount(1, $h2);
        $this->assertEquals("$conferenceTitle Conference", $h2->item(0)->textContent);
    }

    private function assertCommentsAreCorrect(
        Conference $conference,
        DOMXPath $xpathDocument,
        CommentRepository $commentRepository
    ): void {
        $testComments = $commentRepository
            ->getPaginator($conference, 0)
            ->getIterator()
            ->getArrayCopy();

        $commentCount = count($testComments);

        $commentAuthors = $xpathDocument->query('/html/body/h4[text()]');
        $this->assertCount($commentCount, $commentAuthors);
        $commentTexts = $xpathDocument->query('/html/body/p[text()]');
        $this->assertCount($commentCount, $commentTexts);
        $commentDates = $xpathDocument->query('/html/body/small[text()]');
        $this->assertCount($commentCount, $commentDates);

        array_walk($testComments, function ($comment, $i) use ($commentAuthors, $commentTexts, $commentDates) {
            $this->assertEquals($comment->getAuthor(), $commentAuthors->item($i)->textContent);
            $this->assertEquals($comment->getText(), $commentTexts->item($i)->textContent);

            $commentCreatedAtFormatted = $comment->getCreatedAt()->format("M j, Y, h:i A");
            $this->assertEquals($commentCreatedAtFormatted, trim($commentDates->item($i)->textContent));
        });
    }
}
