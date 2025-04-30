<?php

namespace App\Repository;

use App\Entity\Centremedicale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Centremedicale>
 *
 * @method Centremedicale|null find($id, $lockMode = null, $lockVersion = null)
 * @method Centremedicale|null findOneBy(array $criteria, array $orderBy = null)
 * @method Centremedicale[]    findAll()
 * @method Centremedicale[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CentremedicaleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Centremedicale::class);
    }

    public function save(Centremedicale $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /* Fonctions de tri */
    public function findAllSortedByNom(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.nom', $order)
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


    public function findAllSortedByLocalisation(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.localisation', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByDisponibilite(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.disponibilite', $order)
            ->getQuery()
            ->getResult();
    }



//    /**
//     * @return Centremedicale[] Returns an array of Centremedicale objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Centremedicale
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
