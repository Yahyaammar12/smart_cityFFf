<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
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


    public function findAllSortedByCreatedAt(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.created_at', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByRole(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.role', $order)
            ->getQuery()
            ->getResult();
    }


    public function findAllSortedByAdresse(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.adresse', $order)
            ->getQuery()
            ->getResult();
    }





    public function countUsers(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function getMembershipDurationInMonths(): array
    {
        // Get all users with their 'created_at' date
        $users = $this->createQueryBuilder('u')
            ->select('u.id, u.created_at')
            ->getQuery()
            ->getResult();

        // Get the current date
        $currentDate = new \DateTime();

        // Prepare an array to store the results
        $membershipDurations = [];

        // Loop through each user and calculate the membership duration in months
        foreach ($users as $user) {
            // Get the user's created_at date
            $createdAt = $user['created_at'];

            // Calculate the difference in months
            $interval = $createdAt->diff($currentDate);
            $months = ($interval->y * 12) + $interval->m;  // Duration in months

            // Store the result in the array
            $membershipDurations[] = [
                'user_id' => $user['id'],
                'membership_duration_in_months' => $months
            ];
        }

        // Return the array of membership durations
        return $membershipDurations;
    }



    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.role = :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult();
    }

    
//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
