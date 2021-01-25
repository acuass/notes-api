<?php

namespace App\Repository;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Note::class);
        $this->em = $em;
    }


    public function saveNote(string $title, string $note, User $user)
    {
        $newNote = new Note();

        $newNote
            ->setTitle($title)
            ->setNote($note)
            ->setUser($user)
            ->setCreateTime()
            ->setLastUpdated();

        $this->em->persist($newNote);
        $this->em->flush();
    }


    public function updateNote(Note $note): Note
    {
        $this->em->persist($note);
        $this->em->flush();

        return $note;
    }


    public function removeNote(Note $note): Note
    {
        $this->em->remove($note);
        $this->em->flush();

        return $note;
    }

    public function parseNote(Note $note)
    {
        return $note->toArray();
    }


     /**
      * Returns an array of Note objects
      * @param int $id
      *
      * @return Note[]
      */
    public function findAllByUserId(int $id) : array
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.user = :val')
            ->setParameter('val', $id)
            ->orderBy('n.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
