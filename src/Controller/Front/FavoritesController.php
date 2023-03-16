<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Model\MovieModel;
use App\Service\FavoritesManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavoritesController extends AbstractController
{
    /**
     * @Route("/favorites", name="app_favorites_list", methods={"GET"})
     */
    public function list(Request $request): Response
    {
        // on les transmet à la vue pour les afficher
        return $this->render('/front/favorites/list.html.twig');
    }

    /**
     * Ajout d'un favori
     * 
     * id<\d+> équivaut à requirements={"id"="\d+"}
     * 
     * @Route("/favorites/add/{id<\d+>}", name="app_favorites_add", methods={"POST"})
     */
    public function add(Movie $movie = null, FavoritesManager $favoritesManager)
    {
        // si $movie est null, on renvoie une 404
        if ($movie === null) {
            throw $this->createNotFoundException('Film non trouvé.');
        }

        // ajout aux favoris
        $favoritesManager->add($movie);


        // message "flash" stocké en session, à afficher sur la page de redirection
        // @see https://symfony.com/doc/5.4/session.html#flash-messages
        // attention le addFlash renvoi un tableau de message (ici ya qu'un message) qui nous oblige à boucler dessus dans le template
            $this->addFlash(
                'success',
                "<b>{$movie->getTitle()}</b> a été ajouté à votre liste de favoris."
            );

        // on redirige vers la liste
        return $this->redirectToRoute('app_favorites_list');
    }

    /**
     * suppression favoris
     * 
     * id<\d+> équivaut à requirements={"id"="\d+"}
     * 
     * @Route("/favorites/remove/{id<\d+>}", name="app_favorites_remove", methods={"POST"})
     */
    public function remove(Movie $movie, FavoritesManager $favoritesManager) 
    {
        // si $movie est null, on renvoie une 404
        if ($movie === null) {
            throw $this->createNotFoundException('Film non trouvé.');
        }
        if (!$favoritesManager->remove($movie)) {
            throw $this->createNotFoundException('Favoris non trouvé.');
        }
        $favoritesManager->remove($movie);
        
        // on redirige vers la liste
        return $this->redirectToRoute('app_favorites_list');   
    }

    /**
     * suppression de tous les favoris
     * 
     * 
     * @Route("/favorites/empty", name="app_favorites_empty")
     */
    public function empty(FavoritesManager $favoritesManager)
    {
        $favoritesManager->empty();
        return $this->redirectToRoute('app_main_home');
    }
}