<?php

namespace App\Repository;

use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Member>
 *
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    public function add(Member $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Member $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retrieves a list of home members based on their expiration date.
     *
     * @param int $limit The maximum number of members to retrieve.
     * @return array The list of home members.
     */
    public function findMembers()
    {
        $entityManager = $this->getEntityManager();
        //TODO : remettre la requete dans le bon sens avant de déployer
        $query = $entityManager->createQuery(
            'SELECT m, u
            FROM App\Entity\Member m
            JOIN m.user u
            ORDER BY DATE_DIFF(m.membership_expiration, CURRENT_DATE()) ASC
            ');

        return $query->getResult();
    }

    /**
     * Retrieves a list of home members based on their expiration date.
     *
     * @param int $limit The maximum number of members to retrieve.
     * @return array The list of home members.
     */
    public function findMembersHome()
    {
        $entityManager = $this->getEntityManager();
        //TODO : remettre la requete dans le bon sens avant de déployer
        $query = $entityManager->createQuery(
            'SELECT m, u
            FROM App\Entity\Member m
            JOIN m.user u
            WHERE m.membership_statut = 0
            ORDER BY DATE_DIFF(m.membership_expiration, CURRENT_DATE()) ASC
            ');

        return $query->getResult();
    }

//    /**
//     * @return Member[] Returns an array of Member objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Member
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
