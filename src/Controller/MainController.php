<?php

namespace App\Controller;

use App\Model\MovieModel;
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
    public function home()
    {


        $movieModel = new MovieModel() ;

        $movies = $movieModel->getMovies();

        dump($movies);

        return $this->render('main/home.html.twig' , [
            'movies' => $movies,
        ]);
    }

    /**
     * Affiche un film
     * 
     * @Route("/movie/show/{id}", name="app_main_movie_show")
     */
    public function movieShow(int $id)
    {

        // => utiliser le modèle pour récupérer le film concerné
        $movieModel = new MovieModel();
        $movie = $movieModel->getMovieById($id);

        dump($movie);

        return $this->render('main/movie_show.html.twig', [
            'id' => $id,
            'movie' => $movie,
        ]);
        // @todo bonus : rendre cette route dynamique avec l'id du film

    }

    



}