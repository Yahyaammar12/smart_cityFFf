<?php

namespace App\Repository;

use App\Entity\Reclamation;
use App\Entity\Tourisme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 *
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }


    /* Fonctions de tri */
    public function findAllSortedByDate(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.date', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedBySujet(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.sujet', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByRating(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.rating', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedBySolved(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.solved', $order)
            ->getQuery()
            ->getResult();
    }

    

    public function findHighRatedReclamations(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.rating >= :minRating')
            ->setParameter('minRating', 4)
            ->getQuery()
            ->getResult();
    }

    public function countReclamationsByTourisme(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT t.nom AS tourisme_name, COUNT(r.id) AS reclamation_count
            FROM App\Entity\Reclamation r
            JOIN App\Entity\Tourisme t
            WITH r.tourisme_id = t.id
            GROUP BY t.id
            ORDER BY reclamation_count DESC'
        );

        return $query->getResult();
    }


    public function findReclamationsByUserId(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function save(Reclamation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Reclamation[] Returns an array of Reclamation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reclamation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
