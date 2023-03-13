<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Ceci est un préfixe de route, sera ajouté à toutes les routes du contrôleur
 * @Route("/back/season")
 */
class SeasonController extends AbstractController
{
    /**
     * Les saisons d'une série en particulier
     * 
     * @Route("/movie/{id<\d+>}", name="app_back_season_index", methods={"GET"})
     */
    public function index(Movie $movie, SeasonRepository $seasonRepository): Response
    {
        return $this->render('back/season/index.html.twig', [

            // va chercher les saisons dont la série est fournie
            // 'seasons' => $seasonRepository->findBy(['movie' => $movie]),

            // raccourci findBy() pour toutes les propriétés des entités sur le Repository
            'seasons' => $seasonRepository->findByMovie($movie),

            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/new/movie/{id<\d+>}", name="app_back_season_new", methods={"GET", "POST"})
     */
    public function new(Movie $movie, Request $request, SeasonRepository $seasonRepository): Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on associe la saison à la série courant (dans la route)
            $season->setMovie($movie);

            $seasonRepository->add($season, true);

            return $this->redirectToRoute('app_back_season_index', ['id' => $movie->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/season/new.html.twig', [
            'season' => $season,
            'form' => $form,
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{id}/edit/movie/{movie_id<\d+>}", name="app_back_season_edit", methods={"GET", "POST"})
     * @ParamConverter("movie", options={"mapping": {"movie_id": "id"}})
     */
    public function edit(Movie $movie, Request $request, Season $season, SeasonRepository $seasonRepository): Response
    {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // déja associé, car modification

            $seasonRepository->add($season, true);

            return $this->redirectToRoute('app_back_season_index', ['id' => $movie->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/season/edit.html.twig', [
            'season' => $season,
            'form' => $form,
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("/{id}/movie/{movie_id<\d+>}", name="app_back_season_delete", methods={"POST"})
     * @ParamConverter("movie", options={"mapping": {"movie_id": "id"}})     
     * */
    public function delete(Request $request, Season $season, SeasonRepository $seasonRepository, Movie $movie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->request->get('_token'))) {
            $seasonRepository->remove($season, true);
        }

        return $this->redirectToRoute('app_back_season_index', ['id' => $movie->getId()], Response::HTTP_SEE_OTHER);
    }
}
