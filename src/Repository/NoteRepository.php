<?php

namespace App\Repository;

use App\Entity\Note;
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

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }


    public function saveNote(EntityManagerInterface $em, string $title, string $note)
    {
        $newNote = new Note();

        $newNote
            ->setTitle($title)
            ->setNote($note)
            ->setCreateTime()
            ->setLastUpdated();

        $em->persist($newNote);
        $em->flush();
    }



    public function parseData(Note $data)
    {
        return [
            'id' => $data->getId(),
            'title' => $data->getTitle(),
            'note' => $data->getNote(),
            'createTime' => $data->getCreateTime(),
            'lastUpdated' => $data->getLastUpdated(),
        ];
    }


     /**
      * @return Note[] Returns an array of Note objects
      */
    public function findAllByUserId()
    {
        //@TODO: get user by param. filter by it
        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }



    // /**
    //  * @return Note[] Returns an array of Note objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Note
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
