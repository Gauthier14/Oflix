<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiGenreController extends AbstractController
{
    /**
     * Ici on créer la route pour recuperer
     * tous nos genres
     * @Route("/api/genres", name="api_genres_get", methods={"GET"})
     */
    public function getCollection(GenreRepository $genreRepository): Response
    {
        $genreList = $genreRepository->findAll();

        return $this->json(
            $genreList, 
            Response::HTTP_OK, 
            [],
            ['groups' => 'get_genres_collection']
        );
    }
    /**
     * @Route("/api/genres/{id}", name="api_filmByGenre_get", methods={"GET"})
     */
    public function getItemAndMovies(Genre $genre, MovieRepository $movieRepository)
    {
        $movies = $genre->getMovies();

        $data = [
            "genre" => $genre,
            "movies" => $movies
        ];

        return $this->json(
            $data,    
            Response::HTTP_OK, 
            [],
            ['groups' => [
                // les films
                'get_collection',
                // le groupe des genres
                'get_genre_collection'
            ]]
        );
    }
}