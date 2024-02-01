<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function add(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    private function createQueryBuilderForEvents(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.category', 'c')
            ->orderBy('e.start_date', 'DESC');

        return $qb;
    }

    /**
     * Retrieves a list of home events or a single event.
     *
     * @param int|null $id The id of the event, or null for all events.
     * @param int $limit The maximum number of events to retrieve when $id is null.
     * @return array|Event[] An array of events, or a single event.
     */
    public function findEvents($id = null, $limit = null)
    {
        $qb = $this->createQueryBuilderForEvents();

        if ($id !== null) {
            $qb->select('e', 'c', 'm')
                ->leftJoin('e.medias', 'm')
                ->andWhere('e.id = :id')
                ->setParameter('id', $id);
        } elseif ($limit !== null) {
            $qb->select('e', 'c')
                ->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
    /**
     * Retrieves an array of events for the agenda.
     *
     * This function queries the EntityManager to retrieve all events ordered by date in descending order.  It returns an array of these entities.
     *
     * @return array The array of events.
     */
    public function findAllOrderByDate(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT e, c, m
            FROM App\Entity\Event e
            LEFT JOIN e.category c
            LEFT JOIN e.medias m
            ORDER BY e.end_date DESC'
        );

        return $query->getResult();
    }

    public function findByYear($providedYear): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT e
            FROM App\Entity\Event e
            WHERE YEAR(e.start_date) = :providedYear
            ORDER BY e.end_date DESC'
        );
        $query->setParameter('providedYear', $providedYear);

        return $query->getResult();
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
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

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
