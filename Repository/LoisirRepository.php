<?php

namespace App\Repository;

use App\Entity\Loisir;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Loisir>
 *
 * @method Loisir|null find($id, $lockMode = null, $lockVersion = null)
 * @method Loisir|null findOneBy(array $criteria, array $orderBy = null)
 * @method Loisir[]    findAll()
 * @method Loisir[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoisirRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loisir::class);
    }



    /* Fonctions de tri */
    public function findAllSortedByNom(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.nom', $order)
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


    public function findAllSortedByType(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.type', $order)
            ->getQuery()
            ->getResult();
    }

    

//    /**
//     * @return Loisir[] Returns an array of Loisir objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Loisir
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
