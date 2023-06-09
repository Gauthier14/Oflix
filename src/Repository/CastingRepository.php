<?php

namespace App\Repository;

use App\Entity\Movie;
use App\Entity\Casting;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Casting>
 *
 * @method Casting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Casting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Casting[]    findAll()
 * @method Casting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CastingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Casting::class);
    }

    public function add(Casting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Casting $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère les castings et les personnes associées (DQL)
     */
    public function findByMovieJoinedToPerson(Movie $movie)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT c, p -- sélectionne les objets Casting et Person
            FROM App\Entity\Casting AS c -- depuis l entité Casting
            INNER JOIN c.person AS p -- avec une jointure sur Casting.person
            WHERE c.movie = :movie -- où le film du Casting...
            ORDER BY c.creditOrder ASC'
        )->setParameter('movie', $movie); // ... est le film fourni en entrée

        return $query->getResult();
    }

        /**
     * Idem en QueryBuilder
     */
    public function findByMovieJoinedToPersonQb(Movie $movie): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.person', 'p')
            ->addSelect('p')
            ->where('c.movie = :movie')
            ->setParameter('movie', $movie)
            ->orderBy('c.creditOrder')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Casting[] Returns an array of Casting objects
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

//    public function findOneBySomeField($value): ?Casting
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
