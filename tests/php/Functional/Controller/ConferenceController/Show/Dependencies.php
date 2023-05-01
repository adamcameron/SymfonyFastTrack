<?php

namespace adamcameron\symfonythefasttrack\tests\Functional\Controller\ConferenceController\Show;

use adamcameron\symfonythefasttrack\Entity\Comment;
use adamcameron\symfonythefasttrack\Entity\Conference;
use adamcameron\symfonythefasttrack\Repository\CommentRepository;
use ArrayIterator;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Twig\Environment as TwigEnvironment;

trait Dependencies
{
    private function getTestDependencies(string $conferenceTitle): array
    {
        $conference = $this->getMockedConference($conferenceTitle);

        $commentRepository = $this->getMockedCommentRepository();
        return array($conference, $commentRepository);
    }

    private function getMockedConference(string $conferenceTitle): Conference
    {
        $conference = $this->getMockBuilder(Conference::class)
            ->disableOriginalConstructor()
            ->getMock();
        $conference
            ->expects($this->any())
            ->method("__toString")
            ->willReturn($conferenceTitle);
        return $conference;
    }

    private function getMockedCommentRepository(): CommentRepository
    {
        $commentCount = 2;
        $commentsToTestFor = range(1, $commentCount);

        $testComments = array_map(function ($i) {
            return (new Comment())
                ->setAuthor("COMMENTER" . $i)
                ->setText("COMMENT" . $i)
                ->setCreatedAt(new \DateTimeImmutable("2020-01-0{$i}"));
        }, $commentsToTestFor);

        $mockedPaginator = $this->getMockBuilder(Paginator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockedPaginator
            ->method("count")
            ->willReturn($commentCount);
        $mockedPaginator
            ->method("getIterator")
            ->willReturn(new ArrayIterator($testComments));

        $commentRepository = $this->getMockBuilder(CommentRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commentRepository
            ->method("getPaginator")
            ->willReturn($mockedPaginator);

        return $commentRepository;
    }
}
