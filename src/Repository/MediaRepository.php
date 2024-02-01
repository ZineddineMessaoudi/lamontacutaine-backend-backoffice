<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    public function add(Media $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Media $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find medias based on the specified type.
     *
     * @param string $type The type of photos to find.
     * @return array The array of medias that match the specified type.
     */
    public function findMedias($type = "")
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT m, e
            FROM App\Entity\Media m
            JOIN m.event e
            WHERE m.type = :type
            '
        );

        return $query->setParameter('type', $type)->getResult();
    }

    /** 
     * Retrieves an array of flyers.
     *
     * This function queries the EntityManager to retrieve all Media entities
     * with the type 'flyer' and with an end_date greater than today.  It returns an array of these entities.
     *
     * @return array The array of flyers.
     */
    public function getFlyers(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Media m
            JOIN m.event e
            WHERE m.type = :flyer'
            // AND e.end_date > :today'
        );
        $query->setParameter('flyer', 'flyer');
        // $query->setParameter('today', new \DateTime());

        return $query->getResult();
    }

//TODO Changer la condition par galery_slider = 1
    /**
     * Retrieves an array of photos.
     *
     * This function queries the EntityManager to retrieve all Media entities
     * with the galery_slider property set to true. It returns an array of these entities.
     *
     * @return array The array of photos.
     */
    public function getGalerySliderPhotos(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Media m
            JOIN m.event e

            WHERE m.cover_media = 1
            ORDER BY e.end_date DESC'
        );
        $query->setMaxResults(10);
        return $query->getResult();
    }
//TODO
    /**
     * Retrieves an array of photos related to one specific event.
     *
     * This function queries the EntityManager to retrieve all Media entities
     * related to one specific event. It returns an array of these entities.
     *
     * @return array The array of photos.
     */
    public function getPhotosByEvent($eventId): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Media m
            WHERE m.event = :eventId
            AND m.cover_media < 1
            AND m.preview_order IS NULL
            AND m.type = :mediaType'
        );
        $query->setParameter('eventId', $eventId);
        $query->setParameter('mediaType', "image");
        return $query->getResult();
    }

    /**
     * Retrieves an array of flyers related to one specific event.
     *
     * This function queries the EntityManager to retrieve all Media entities
     * related to one specific event. It returns an array of these entities.
     *
     * @return array The array of photos.
     */
    public function getFlyersByEvent($eventId): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Media m
            WHERE m.event = :eventId
            AND m.type = :mediaType'
        );
        $query->setParameter('eventId', $eventId);
        $query->setParameter('mediaType', "flyer");
        return $query->getResult();
    }

    /**
     * Retrieves an array of cover photo related to one specific event.
     *
     * This function queries the EntityManager to retrieve all Media entities
     * related to one specific event. It returns an array of these entities.
     *
     * @return array The array of photos.
     */
    public function getCoversByEvent($eventId): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Media m
            WHERE m.event = :eventId
            AND m.cover_media = 1'
        );
        $query->setParameter('eventId', $eventId);
        return $query->getResult();
    }

        /**
     * Retrieves an array of photos preview (used on event presentation in agenda) related to one specific event.
     *
     * This function queries the EntityManager to retrieve all Media entities
     * related to one specific event. It returns an array of these entities.
     *
     * @return array The array of photos.
     */
    public function getPreviewsByEvent($eventId): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Media m
            WHERE m.event = :eventId
            AND m.preview_order > 0
            AND m.type = :mediaType
            ORDER BY m.preview_order ASC'
        );
        $query->setParameter('eventId', $eventId);
        $query->setParameter('mediaType', "image");
        return $query->getResult();
    }

    //    /**
    //     * @return Media[] Returns an array of Media objects
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

    //    public function findOneBySomeField($value): ?Media
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
