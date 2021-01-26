<?php

namespace App\Controller;


use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use App\Service\UserHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class NoteController
{
    private $noteRepository;
    private $userRepository;
    private $userHelper;

    public function __construct(NoteRepository $noteRepository, UserRepository $userRepository, UserHelper $userHelper)
    {
        $this->noteRepository = $noteRepository;
        $this->userRepository = $userRepository;
        $this->userHelper = $userHelper;
    }

    /**
     * @Route("/notes", name="add_note", methods={"POST"})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $title = $data['title'];
        $note = $data['note'];

        $user = $this->userHelper->checkUserAuthorization($request->headers->get("Authorization"));
        //$user = $this->userHelper->isUserAuthorizedByBasicHttp($request);
        if (empty($user)) {
            return new JsonResponse(['status' => 'You are not authorized!'], Response::HTTP_UNAUTHORIZED);
        }


        if (empty($title) || empty($note)) {
            return new JsonResponse(['status' => 'Expecting mandatory parameters!'], Response::HTTP_BAD_REQUEST);
        }

        $this->noteRepository->saveNote($title, $note, $user);

        return new JsonResponse(['status' => 'Note created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/notes", name="get_all_user_notes", methods={"GET"})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAllByUserId(Request $request): JsonResponse
    {
        $user = $this->userHelper->checkUserAuthorization($request->headers->get("Authorization"));
        //$user = $this->userHelper->isUserAuthorizedByBasicHttp($request);
        if (empty($user)) {
            return new JsonResponse(['status' => 'You are not authorized!'], Response::HTTP_UNAUTHORIZED);
        }

        $notes = $this->noteRepository->findAllByUserId($user->getId());

        $data = [];
        foreach ($notes as $note) {
            $data[] = $this->noteRepository->parseNote($note);
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/notes/{id}", name="get_one_note", methods={"GET"})
     * @param int $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getOne($id, Request $request): JsonResponse
    {
        $note = $this->noteRepository->findOneBy(['id' => $id]);
        if (empty($note)) {
            return new JsonResponse(['status' => 'The note does not exist'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->userHelper->checkUserAuthorization($request->headers->get("Authorization"));
        //$user = $this->userHelper->isUserAuthorizedByBasicHttp($request);
        if (empty($user) || !$this->userHelper->isUserOwnerOfNote($user,$note)) {
            return new JsonResponse(['status' => 'You are not authorized!'], Response::HTTP_UNAUTHORIZED);
        }


        return new JsonResponse($note->toArray(), Response::HTTP_OK);
    }


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

        if (empty($note)) {
            return new JsonResponse(['status' => 'The note does not exist'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->userHelper->checkUserAuthorization($request->headers->get("Authorization"));
        //$user = $this->userHelper->isUserAuthorizedByBasicHttp($request);
        if (empty($user) || !$this->userHelper->isUserOwnerOfNote($user,$note)) {
            return new JsonResponse(['status' => 'You are not authorized!'], Response::HTTP_UNAUTHORIZED);
        }

        //@TODO: can the ownership of a note change to another user? Assume no for now
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function delete(int $id, Request $request): JsonResponse
    {
        $note = $this->noteRepository->findOneBy(['id' => $id]);
        if (empty($note)) {
            return new JsonResponse(['status' => 'The note does not exist'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->userHelper->checkUserAuthorization($request->headers->get("Authorization"));
        //$user = $this->userHelper->isUserAuthorizedByBasicHttp($request);
        if (empty($user) || !$this->userHelper->isUserOwnerOfNote($user,$note)) {
            return new JsonResponse(['status' => 'You are not authorized!'], Response::HTTP_UNAUTHORIZED);
        }

        $this->noteRepository->removeNote($note);

        return new JsonResponse(['status' => 'Note deleted'], Response::HTTP_NO_CONTENT);
    }
}