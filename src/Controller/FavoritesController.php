<?php

namespace App\Controller;

use App\Model\MovieModel;
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
        $session = $request->getSession();

        // on récupère les favoris ou un tableau vide
        $favorites = $session->get('favorites', []); // $favorites = $_SESSION['favorites'];

        // TWIG PEUT ACCEDER DIRECTEMENT A LA SESSION VIA app.session.get('favorites')
        dump($session);

        // on les transmet à la vue pour les afficher
        return $this->render('favorites/list.html.twig', [
            'favoris' => $favorites
        ]);
    }

    /**
     * Ajout d'un favori
     * 
     * id<\d+> équivaut à requirements={"id"="\d+"}
     * 
     * @Route("/favorites/add/{id<\d+>}", name="app_favorites_add", methods={"POST"})
     */
    public function add(Request $request, int $id)
    {
        // on récupère la session qui se trouve dans la requête
        $session = $request->getSession();

        $movieModel = new MovieModel();
        $movie = $movieModel->getMovieById($id);

        // si $movie est null, on renvoie une 404
        if ($movie === null) {
            throw $this->createNotFoundException('Film non trouvé.');
        }

        // on récupère les favoris déjà présents,
        // ou un tableau vide si aucun favoris présents
        $favorites = $session->get('favorites', []);

        // on y ajoute (on push à la fin du tableau) l'id du film à mettre en favoris
        $favorites[$id] = $movie;

        // on écrase les favoris en session par cette nouvelle liste de favoris
        $session->set('favorites', $favorites); // $_SESSION['favorites'] = $favorites;

        // message "flash" stocké en session, à afficher sur la page de redirection
        // @see https://symfony.com/doc/5.4/session.html#flash-messages
        // attention le addFlash renvoi un tableau de message (ici ya qu'un message) qui nous oblige à boucler dessus dans le template
        $this->addFlash(
            'success',
            "<b>{$movie['title']}</b> a été ajouté à votre liste de favoris."
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
    public function remove(Request $request, int $id)
    {
    
    // je recup les favoris dans la session : 
    $session = $request->getSession();
    $favorites = $session->get('favorites', []);
    
    // je supp le favoris ciblé 
    unset($favorites[$id]);

    // j'actualise les données de session 

    $session->set('favorites', $favorites);



    // on redirige vers la liste
    return $this->redirectToRoute('app_favorites_list');
            
    }

    /**
     * suppression de tous les favoris
     * 
     * 
     * @Route("/favorites/empty", name="app_favorites_empty")
     */
    public function empty(Request $request)
    {
        $session = $request->getSession();
        $session->clear();

        // on redirige 
        return $this->redirectToRoute('app_main_home');
    }
}