<?php 

namespace App\Service;

use App\Entity\Movie;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Gestion des films et séries favoris
 */
class FavoritesManager {

    // @see https://symfony.com/doc/current/session.html
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }



    /**
     * add favoris
     * @param Movie $movie Film ou série
     */
    public function add (Movie $movie) {
        // on récupère la session qui se trouve dans la requête
        $session = $this->requestStack->getSession() ;

        // on récupère les favoris déjà présents,
        // ou un tableau vide si aucun favoris présents
        $favorites = $session->get('favorites', []);

        // on y ajoute (on push à la fin du tableau) l'id du film à mettre en favoris
        $favorites[$movie->getId()] = $movie;

        // on écrase les favoris en session par cette nouvelle liste de favoris
        $session->set('favorites', $favorites); // $_SESSION['favorites'] = $favorites;
    }

    /**
     * suppression
     * @param Movie $movie du film / série
     */
    public function remove(Movie $movie)
    {
        $session = $this->requestStack->getSession();

        $favorites = $session->get('favorites', []);

        // si le film est non trouvé, on renvoie une 404
        if (!array_key_exists($movie->getId(), $favorites)) {
                return false;
            }

        // on supprime l'élément à la clé du tableau
        // @see https://www.php.net/manual/fr/function.unset.php
        unset($favorites[$movie->getId()]);

        // on écrase les favoris en session par cette nouvelle liste de favoris
        $session->set('favorites', $favorites); // $_SESSION['favorites'] = $favorites;

        return true;
    }

    /**
     * vide les favoris
     */ 
    public function empty()
    {
        $session = $this->requestStack->getSession() ;
        $session->clear();
    }

    /**
     * Liste des favoris
     */
    public function list () {
        // rien a écrire car géré dans la vue
    }


}