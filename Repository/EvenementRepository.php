<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 *
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }


    /* Fonctions de tri */
    public function findAllSortedByNom(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.nom', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByDate(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.date', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByHeure(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.heure', $order)
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

    

    public function getEvenementsByLoisirId(): array
    {
        return $this->createQueryBuilder('d')
            ->select('l.nom AS loisir_name, COUNT(d.id) AS event_count')
            ->join('d.loisir', 'l')
            ->groupBy('l.id')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Evenement[] Returns an array of Evenement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evenement
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
