<?php

namespace App\Controller\Front;

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


        return $this->render('front/main/home.html.twig' , [
            'movies' => $movies,
            'genres' => $genres,
        ]);
    }

    /**
     * Affiche un film
     * 
     * @Route("/movie/show/{slug}", name="app_main_movie_show")
     */
    public function movieShow(Movie $movie = null, CastingRepository $castingRepository, ReviewRepository $reviewRepository)
    {
        // le film a été récupéré par le ParamConverter de Symfony


        // si $movie est null, on renvoie une 404
        if ($movie === null) {
            // @see https://symfony.com/doc/5.4/controller.html#managing-errors-and-404-pages
            throw $this->createNotFoundException('Film non trouvé.');
        }

        $castings = $castingRepository->findByMovieJoinedToPerson($movie);

                // Reviews
        // dans le cas où on a pas la relation inverse
        // on peut aller chercher les données dans le Repository
        $reviews = $reviewRepository->findBy(['movie' => $movie]);


        // on transmet le film à la vue
        return $this->render('front/main/movie_show.html.twig', [
            'movie' => $movie,
            'castings' => $castings,
            'reviews' => $reviews,
        ]);
    }

    /**
     * Page "Films et séries"
     * 
     * @Route("/movies", name="app_main_movies_list", methods={"GET"})
     * @Route("/search", name="app_main_search", methods={"GET"})
     */
    public function moviesList(MovieRepository $movieRepository, GenreRepository $genreRepository, Request $request)
    {

        // un mot-clé de recherche est-il présent ?
        $keyword = $request->query->get('keyword');


        $movies = $movieRepository->findAllOrderedByTitleAscQb($keyword);
        
        return $this->render('front/main/movie_list.html.twig', [
            'movies' => $movies,
            'genres' => $genreRepository->findBy([], ['name' => 'ASC']),
            'keyword' => $keyword,
        ]);
    }


    /**
     * Add reviews
     * 
     * @Route("/movie/{id}/review/add", name="app_main_review_add", requirements={"id"="\d+"},  methods={"GET", "POST"})
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

            return $this->redirectToRoute('app_main_movie_show', ['slug' => $movie->getSlug()]);
        }


        return $this->renderForm('front/main/review_add.html.twig', [
            'form' => $form,
            'movie' => $movie,
        ]);
        
    }
    



}