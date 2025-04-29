<?php

namespace App\Repository;

use App\Entity\Transport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transport>
 *
 * @method Transport|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transport|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transport[]    findAll()
 * @method Transport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transport::class);
    }

    public function save(Transport $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /* Fonctions de tri */
    public function findAllSortedByPlacesLibres(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.places_libres', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByType(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.type', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByTarif(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.tarif', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByHoraire(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.horaire', $order)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Transport[] Returns an array of Transport objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Transport
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
