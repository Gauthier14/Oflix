<?php

namespace App\Controller\Api;


use App\Entity\Movie;
use App\Service\MySlugger;
use App\Repository\MovieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiMovieController extends AbstractController
{
    /**
     * Retourne le données en JSON
     * 
     * @Route("/api/movies", name="api_movies_get", methods={"GET"})
     */
    public function getCollection(MovieRepository $movieRepository)
    {
        $movies = $movieRepository->findAll();



        return $this->json(
            $movies,
            200,
            [],
            ['groups' => 'get_collection']
        );
    }

        /**
     * Requete pour chercher un Item
     *
     * @Route("/api/movies/{id<\d+>}", name="api_movies_get_item", methods={"GET"})
     */
    public function getItem(?Movie $movie, SerializerInterface $serializer)
    {
        // $test = $serializer->serialize($movie, 'json',['groups' => 'get_collection']);
        // dd($test);
        {
            return $this->json([
                'error' => "écrit non trouvé",
                response::HTTP_NOT_FOUND
            ]);
        }
        return $this->json(
            $movie,
            200,
            [],
            ['groups' => 'get_item']
        );
    }

        /**
     * @Route("api/movies/random",   name="api_movies_get_item_random", methods={"GET"})
     */
    public function getItemRandom(MovieRepository $movieRepository)
    {
        // On va chercher le film random
        // Pour se faire, utiliser findOneRandomMovie()
        // Qui est dans MovieRepository
        $randomMovie = $movieRepository->findOneRandomMovieDQL();

        return $this->json(
            $randomMovie,
            Response::HTTP_OK,
            [],
            ['groups' => 'get_item']
        );
    }

    /**
     * @Route("/api/movies", name="api_movies_post", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine, MySlugger $mySlugger, ValidatorInterface $validatorInterface)
    {
        // On recuperer le json
        $jsonContent = $request->getContent();

        try 
        {
        // On deserialize (convertir) le json en entité movie
        $movie = $serializer->deserialize($jsonContent, Movie::class, 'json');
        } 
        catch (NotEncodableValueException $e) 
        {
            return $this->json(
                ["error" => "JSON INVALIDE"],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $errors = $validatorInterface->validate($movie);

        if(count($errors) > 0)
        {
            return $this->json(
                $errors, Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }


        // On sauvegarde l'entité
        $entityManager = $doctrine->getManager();
        $movie->setSlug($mySlugger->slugify($movie->getTitle()));
        $entityManager->persist($movie);
        $entityManager->flush();

        // On retorune la reponse adapté

        return $this->json(
            // Le film crée
            $movie,
            // Le status code 201 : CREATED
            Response::HTTP_CREATED,
            [
                // Location = /api/movies/{id_du_film_crée}
                'Location' => $this->generateUrl('api_movies_get_item', ['id' => $movie->getId()])
            ],
            ['groups' => 'get_item']
        );
    }
}