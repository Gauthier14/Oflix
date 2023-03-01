<?php

namespace App\Controller;

use App\Model\MovieModel;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * Retourne le données en JSON
     * 
     * @Route ("/api/movies", name="app_api_get_movies")
     */
    public function getMovies()
    {
        $movieModel = new MovieModel();
        $movies = $movieModel->getMovies();

        return $this->json($movies);
    }
}