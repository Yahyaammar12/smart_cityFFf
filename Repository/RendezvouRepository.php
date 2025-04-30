<?php

namespace App\Repository;

use App\Entity\Rendezvou;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rendezvou>
 *
 * @method Rendezvou|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rendezvou|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rendezvou[]    findAll()
 * @method Rendezvou[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RendezvouRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rendezvou::class);
    }


    /* Fonctions de tri */
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


    public function findAllSortedByStatut(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.status', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByNomMedecin(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.nomMedecin', $order)
            ->getQuery()
            ->getResult();
    }
    

    public function getRdvsByCentreId(): array
    {
        return $this->createQueryBuilder('d')
            ->select('l.nom AS centre_name, COUNT(d.id) AS rdv_count')
            ->join('d.centremedicale', 'l')
            ->groupBy('l.id')
            ->getQuery()
            ->getResult();
    }

    public function findRendezVousByUserId(int $userId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function save(Rendezvou $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Rendezvou[] Returns an array of Rendezvou objects
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

//    public function findOneBySomeField($value): ?Rendezvou
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
