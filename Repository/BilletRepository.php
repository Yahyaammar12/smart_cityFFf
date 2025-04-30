<?php

namespace App\Repository;

use App\Entity\Billet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Billet>
 *
 * @method Billet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Billet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Billet[]    findAll()
 * @method Billet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BilletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Billet::class);
    }


    /* Fonctions de tri */
    public function findAllSortedByStatutPaiement(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.payment_status', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByStatut(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.status', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByPrix(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.prix', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByDate(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.date_voyage', $order)
            ->getQuery()
            ->getResult();
    }


    


    public function countBillets(): int
    {
        return (int) $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    
    public function findBilletsByUserId(int $userId): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function save(Billet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Billet[] Returns an array of Billet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Billet
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
