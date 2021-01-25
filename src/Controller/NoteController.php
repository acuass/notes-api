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
     *
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $title = $data['title'];
        $note = $data['note'];

        $user = $this->isUserAuthorizedByBasicHttp($request);
        if (empty($user)) {
            return new JsonResponse(['status' => 'You are not authorized!'], Response::HTTP_UNAUTHORIZED);
        }


        if (empty($title) || empty($note)) {
            //@TODO: return bad request
            throw new NotFoundHttpException('Expecting mandatory parameters!');
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
        $user = $this->isUserAuthorizedByBasicHttp($request);
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

        if (empty($note)) {
            return new JsonResponse(['status' => 'The note does not exist'], Response::HTTP_NOT_FOUND);
        }

        $user = $this->isUserAuthorizedByBasicHttp($request);
        if (empty($user) || $note->getUser()->getEmail() != $user->getEmail()) {
            return new JsonResponse(['status' => 'You are not authorized!'], Response::HTTP_UNAUTHORIZED);
        }

        //@TODO: can a note change the ownership to another user? Assume no for now
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

        $user = $this->isUserAuthorizedByBasicHttp($request);
        if (empty($user) || $note->getUser()->getEmail() != $user->getEmail()) {
            return new JsonResponse(['status' => 'You are not authorized!'], Response::HTTP_UNAUTHORIZED);
        }

        $this->noteRepository->removeNote($note);

        return new JsonResponse(['status' => 'Note deleted'], Response::HTTP_NO_CONTENT);
    }


    /**
     * Returns the user if the access is granted. Null otherwise
     * @param Request $request
     *
     * @return \App\Entity\User|null
     */
    private function isUserAuthorizedByBasicHttp(Request $request) {
        //@TODO: different types of authorization? Explode, and with a factory get the correct manager based on the type (basic, bearer, etc...)
        $auth = $request->headers->get("Authorization");
        if (empty($auth)) return null;

        $auth = explode(" ", $auth);
        $auth = base64_decode($auth[1]);
        $auth = explode(":", $auth);

        $user = $this->userRepository->findOneBy(['email' => $auth[0]]);

        //@TODO:password should be encoded in DB.
        return !empty($user) && $user->getPassword() == $auth[1] ? $user : null;
    }
}