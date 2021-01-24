<?php

namespace App\Controller;


use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NoteController
{
    private $noteRepository;
    private $userRepository;

    public function __construct(NoteRepository $noteRepository, UserRepository $userRepository)
    {
        $this->noteRepository = $noteRepository;
        $this->userRepository = $userRepository;
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
        $user = $data['user'];

        //@TODO: decide if search of user by id or email
        $user = $this->userRepository->findOneBy(['id' => $user]);

        if (empty($title) || empty($note) || empty($user)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->noteRepository->saveNote($title, $note, $user);

        return new JsonResponse(['status' => 'Note created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/notes", name="get_all_user_notes", methods={"GET"})
     */
    public function getAllByUserId(): JsonResponse
    {
        //@TODO: receive user id as param. match notes for a specific user
        $notes = $this->noteRepository->findAllByUserId();
//        $notes = $this->noteRepository->findAllByUserId(['id' => $id]);

        $data = [];
        foreach ($notes as $note) {
            $data[] = $this->noteRepository->parseNote($note);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/notes/{id}", name="get_one_note", methods={"GET"})
     */
//    public function get($id): JsonResponse
//    {
//        //@TODO: check note is from the user
//        $notes = $this->noteRepository->findOneBy(['id' => $id]);
//
//
//        return new JsonResponse($data, Response::HTTP_OK);
//    }


    /**
     * @Route("/notes/{id}", name="update_note", methods={"PUT"})
     * @param int $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $note = $this->noteRepository->findOneBy(['id' => $id]);
        //@TODO: validate the note exists.
        //@TODO: Validate the user is the owner of the note

        $data = json_decode($request->getContent(), true);

        empty($data['title']) ? true : $note->setTitle($data['title']);
        empty($data['note']) ? true : $note->setNote($data['note']);
        $note->setLastUpdated();

        $updatedNote = $this->noteRepository->updateNote($note);

        return new JsonResponse($updatedNote->toArray(), Response::HTTP_OK);
    }


    /**
     * @Route("/notes/{id}", name="delete_note", methods={"DELETE"})
     * @param int $id
     *
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $note = $this->noteRepository->findOneBy(['id' => $id]);

        //@TODO: validate the note exists
        //@TODO: Validate the user is the owner of the note

        $this->noteRepository->removeNote($note);

        return new JsonResponse(['status' => 'Note deleted'], Response::HTTP_OK);
    }
}