<?php

namespace App\Controller;


use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NoteController
{
    private $noteRepository;
    private $entityManager;

    public function __construct(NoteRepository $noteRepository, EntityManagerInterface $entityManager)
    {
        $this->noteRepository = $noteRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/notes", name="add_note", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $title = $data['title'];
        $note = $data['note'];

        if (empty($title) || empty($note)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->noteRepository->saveNote($this->entityManager, $title, $note);

        return new JsonResponse(['status' => 'Note created!'], Response::HTTP_CREATED);
    }
}