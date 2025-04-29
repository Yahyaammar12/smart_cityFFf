<?php

namespace App\Repository;

use App\Entity\Demandeservice;
use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Demandeservice>
 *
 * @method Demandeservice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Demandeservice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Demandeservice[]    findAll()
 * @method Demandeservice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeserviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demandeservice::class);
    }


    /* Fonctions de tri */
    public function findAllSortedByDate(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.date', $order)
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


    public function findAllSortedByRating(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.rating', $order)
            ->getQuery()
            ->getResult();
    }


    
    public function getServiceRequestStats(): array
    {
        $entityManager = $this->getEntityManager();

        // Total number of services
        $totalServices = $entityManager->createQuery(
            'SELECT COUNT(s.id) FROM App\Entity\Service s'
        )->getSingleScalarResult();

        // Number of services that have at least one demande
        $servicesWithDemandes = $entityManager->createQuery(
            'SELECT COUNT(DISTINCT s.id)
            FROM App\Entity\DemandeService ds
            JOIN ds.service s'
        )->getSingleScalarResult();

        // Total number of demandes
        $totalDemandes = $entityManager->createQuery(
            'SELECT COUNT(ds.id) FROM App\Entity\DemandeService ds'
        )->getSingleScalarResult();

        // Percentage of services that have at least one demande
        $percentageOfServicesRequested = $totalServices > 0
            ? ($servicesWithDemandes / $totalServices) * 100
            : 0;

        // Percentage of demandes that correspond to a service (platform services)
        $percentageOfDemandesWithService = $totalDemandes > 0
            ? ($totalDemandes / $totalServices) * 100
            : 0;

        return [
            'total_services' => $totalServices,
            'services_with_demandes' => $servicesWithDemandes,
            'total_demandes' => $totalDemandes,
            'percentage_services_requested' => round($percentageOfServicesRequested, 2),
            'percentage_demandes_per_service' => round($percentageOfDemandesWithService, 2),
        ];
    }


    public function findDemandeServicesByUserId(int $userId): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function getDemandeServicesByServiceId(): array
    {
        return $this->createQueryBuilder('d')
            ->select('s.nom AS service_name, COUNT(d.id) AS demande_count')
            ->join('d.service', 's')
            ->groupBy('s.id')
            ->getQuery()
            ->getResult();
    }


    public function save(Demandeservice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Demandeservice[] Returns an array of Demandeservice objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Demandeservice
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
