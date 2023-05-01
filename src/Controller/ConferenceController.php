<?php

namespace adamcameron\symfonythefasttrack\Controller;

use adamcameron\symfonythefasttrack\Entity\Conference;
use adamcameron\symfonythefasttrack\Repository\CommentRepository;
use adamcameron\symfonythefasttrack\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConferenceController extends AbstractController
{
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        return $this->render(
            "conference/index.html.twig",
            ["conferences" => $conferenceRepository->findAll()]
        );
    }

    public function show(
        Request $request,
        Conference $conference,
        CommentRepository $commentRepository
    ): Response {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getPaginator($conference, $offset);

        return $this->render(
            "conference/show.html.twig",
            [
                "conference" => $conference,
                "comments" => $paginator,
                'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
                'next' => min(count($paginator), $offset + CommentRepository::PAGINATOR_PER_PAGE)
            ]
        );
    }
}
