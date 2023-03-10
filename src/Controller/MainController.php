<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Model\MovieModel;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use App\Repository\ReviewRepository;
use App\Repository\CastingRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * Page d'accueil
     * 
     * @Route("/", name="app_main_home")
     */
    public function home(MovieRepository $movieRepository, GenreRepository $genreRepository)
    {



        // on récupère la liste des films lesp lus récents (dump() pour vérifier)
        $movies = $movieRepository->findBy([], ['releaseDate' => 'DESC']);

        // les genres
        $genres = $genreRepository->findBy([], ['name' => 'ASC']);

        dump($genres);

        return $this->render('main/home.html.twig' , [
            'movies' => $movies,
            'genres' => $genres,
        ]);
    }

    /**
     * Affiche un film
     * 
     * @Route("/movie/show/{id}", name="app_main_movie_show", requirements={"id"="\d+"})
     */
    public function movieShow(Movie $movie = null, CastingRepository $castingRepository)
    {
        // le film a été récupéré par le ParamConverter de Symfony
        dump($movie);


        // si $movie est null, on renvoie une 404
        if ($movie === null) {
            // @see https://symfony.com/doc/5.4/controller.html#managing-errors-and-404-pages
            throw $this->createNotFoundException('Film non trouvé.');
        }

        $castings = $castingRepository->findByMovieJoinedToPerson($movie);
        dump($castings);

        // on transmet le film à la vue
        return $this->render('main/movie_show.html.twig', [
            'movie' => $movie,
            'castings' => $castings,
        ]);
    }

    /**
     * Page "Films et séries"
     * 
     * @Route("/movies", name="app_main_movies_list", methods={"GET"})
     */
    public function moviesList(MovieRepository $movieRepository)
    {
        $movies = $movieRepository->findAllOrderedByTitleAscQb();
        dd($movies);
    }


    /**
     * Add reviews
     * 
     * @Route("//movie/{id}/review/add", name="app_main_review_add", requirements={"id"="\d+"},  methods={"GET", "POST"})
     */
    public function add(ReviewRepository $reviewRepository, Request $request, Movie $movie)
    {  

        // on crée un objet post
        $review = new Review();

        // 2ème argument $data = valeurs par défaut du form, ici, les propriétés de l'entité
        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $review->setMovie($movie);

            $reviewRepository->add($review, true);

            return $this->redirectToRoute('app_main_home');
        }


        return $this->renderForm('main/review_add.html.twig', [
            'form' => $form,
            'movie' => $movie,
        ]);
        
    }
    



}