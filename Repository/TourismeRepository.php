<?php

namespace App\Repository;

use App\Entity\Tourisme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tourisme>
 *
 * @method Tourisme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tourisme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tourisme[]    findAll()
 * @method Tourisme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TourismeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tourisme::class);
    }



    /* Fonctions de tri */
    public function findAllSortedByType(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.type', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByNbEtoiles(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.nb_etoiles', $order)
            ->getQuery()
            ->getResult();
    }

    public function findAllSortedByLocalisation(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.localisation', $order)
            ->getQuery()
            ->getResult();
    }

    public function findAllSortedByNom(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.nom', $order)
            ->getQuery()
            ->getResult();
    }

    


    public function getSatisfactionPercentage(): float
    {
        $qb = $this->createQueryBuilder('t')
            ->select('SUM(t.nb_etoiles) as totalRating', 'COUNT(t.id) as totalTourismes')
            ->getQuery()
            ->getOneOrNullResult();

        if ($qb['totalTourismes'] == 0 || $qb['totalRating'] === null) {
            return 0;
        }

        $maxRating = 5;
        $percentage = ($qb['totalRating'] / ($qb['totalTourismes'] * $maxRating)) * 100;

        return round($percentage, 2); // On arrondit à 2 décimales
    }

//    /**
//     * @return Tourisme[] Returns an array of Tourisme objects
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

//    public function findOneBySomeField($value): ?Tourisme
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
