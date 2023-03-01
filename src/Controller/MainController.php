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
     * @Route("/movie/show/{id}", name="app_main_movie_show", requirements={"id"="\d+"})
     */
    public function movieShow(int $id)
    {
        dump($id);
        // => utiliser le modèle pour récupérer le film concerné
        // on crée une instance de la classe MovieModel
        $movieModel = new MovieModel();
        // puis on utilise ses méthodes
        $movie = $movieModel->getMovieById($id);
        dump($movie);
        // si $movie est null, on renvoie une 404
        if ($movie === null) {
            // @see https://symfony.com/doc/5.4/controller.html#managing-errors-and-404-pages
            throw $this->createNotFoundException('Film non trouvé.');
        }

        // on transmet le film à la vue
        return $this->render('main/movie_show.html.twig', [
            'movie' => $movie,
            'id' => $id
        ]);
    }

    



}