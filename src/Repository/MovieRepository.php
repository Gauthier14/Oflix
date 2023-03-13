<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 *
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function add(Movie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Movie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Liste des films par ordre alphabétique (DQL)
     * @see https://symfony.com/doc/current/doctrine.html#querying-for-objects-the-repository
     */
    public function findAllOrderedByTitleAscDql()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Movie AS m
            ORDER BY m.title ASC'
        );

        // returns an array of Product objects
        return $query->getResult();
    }

    /**
     * Liste des films par ordre alphabétique (QB)
     * 
     * @param string $keyword Mot-clé de recherche
     * 
     * @see https://symfony.com/doc/current/doctrine.html#querying-for-objects-the-repository
     */
    public function findAllOrderedByTitleAscQb(?string $keyword)
    {
        // on crée un QueryBuilder
        $qb = $this->createQueryBuilder('m')
            ->orderBy('m.title', 'ASC');
        
        // y'a-t-il une recherche ?
        if ($keyword !== null) {
            // on ajoute un condition à la requête
            $qb->where('m.title LIKE :keyword')
                ->setParameter('keyword', '%'.$keyword.'%');
        }

        // on retourne l'exécution de la requête
        return $qb->getQuery()->getResult();
    }

    /**
     * Liste des films les plus récents (QB)
     * 
     * @see https://symfony.com/doc/current/doctrine.html#querying-for-objects-the-repository
     */
    public function findAllForHomePage()
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.releaseDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Movie[] Returns an array of Movie objects
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

//    public function findOneBySomeField($value): ?Movie
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
