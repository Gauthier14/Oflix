<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Season;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Classe de test BREAD/CRUD pour manipuler l'entité Movie
 */
class MovieTestController extends AbstractController
{
    /**
     * Browse => liste, find all
     * 
     * @Route("/test/movie/browse")
     */
    public function browse(MovieRepository $movieRepository)
    {
        // @see https://symfony.com/doc/5.4/doctrine.html#fetching-objects-from-the-database

        // on va chercher les films dans le repository de l'entité
        $movies = $movieRepository->findAll();
        // dump and die / => dump et stop le programme
        dd($movies);
    }

    /**
     * Read => un item, find by id
     */
    public function read()
    {
        # code...
    }

    /**
     * Edit => update item
     * 
     * @Route("/test/movie/edit/{id<\d+>}")
     */
    public function edit(Movie $movie, EntityManagerInterface $em)
    {
        // ajoutons un nouveau genre à ce film
        $genre = new Genre();
        $genre->setName('Comédie');

        // on associe depuis celui qui détient
        $movie->addGenre($genre);

        // on persist, on flush
        $em->persist($genre);
        $em->flush();

        dd($movie);
    }

    /**
     * Add => ajoute
     * 
     * @Route("/test/movie/add")
     */
    public function add(ManagerRegistry $doctrine)
    {
        // @see https://symfony.com/doc/5.4/doctrine.html#persisting-objects-to-the-database

        // l'entity Manager permet les actions d'écriture dans la base
        $entityManager = $doctrine->getManager();

        // on crée un objet Movie
        $movie = new Movie();
        // on défini ses propriétés
        $movie->setTitle('Game of Newtons');
        $movie->setReleaseDate(new \DateTime('2024-08-01'));
        $movie->setDuration(52);
        $movie->setSummary('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellendus nulla totam assumenda. Quibusdam, nam dolorum suscipit itaque a ab beatae quam in dolores voluptatum laboriosam exercitationem.');
        $movie->setSynopsis('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellendus nulla totam assumenda. Quibusdam, nam dolorum suscipit itaque a ab beatae quam in dolores voluptatum laboriosam exercitationem iusto optio nihil sit quos alias reiciendis molestiae, reprehenderit provident velit ut autem vel dolore! Ea in quidem corporis voluptates laborum ipsam. Suscipit fugiat mollitia quae dolorum, ducimus blanditiis distinctio vel provident ullam eum recusandae id voluptatum labore sequi autem nemo, commodi ut, voluptatem iure ipsam voluptates. Quaerat voluptas aliquam nam commodi. Temporibus incidunt laudantium, optio eum tenetur reprehenderit earum, consequuntur similique quis nisi sint corrupti tempora repellat unde, nemo quas quasi quae explicabo distinctio at! Officiis, debitis repellendus autem quisquam repudiandae unde eius ipsa nisi ad, nam doloribus vitae assumenda consequuntur rem provident magni cupiditate, iure molestiae asperiores iste cumque laboriosam veritatis aut neque? Soluta repudiandae debitis fugit temporibus ut quia quaerat sapiente esse molestias obcaecati, cupiditate quidem voluptatibus officia doloremque assumenda quisquam.');
        $movie->setPoster('https://images.unsplash.com/photo-1445543949571-ffc3e0e2f55e?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=869&q=80');
        $movie->setRating('4.7');

        // on crée une saison
        $season = new Season();
        $season->setNumber(1);
        $season->setEpisodesNumber(6);
        // on associe la série/le film à la saison
        // Doctrine nous conseille d'associer du côté de celle qui détient (la ManyToOne)
        $season->setMovie($movie);

        dump($movie);
        
        // on demande à Doctrine de s'apprêter à persister l'entité
        $entityManager->persist($movie);
        // on persiste aussi l'entité $season
        $entityManager->persist($season);

        // on exécute la requête SQL
        $entityManager->flush();

        dd($movie);
    }

    /**
     * Delete => supprime
     */
    public function delete()
    {
        # code...
    }
}