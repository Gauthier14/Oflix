<?php

namespace App\EventSubscriber;

use App\Controller\Front\MainController;
use App\Repository\MovieRepository;
use Twig\Environment as Twig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class RandomMovieSubscriber implements EventSubscriberInterface
{
    private $movieRepository;
    private $twig;

    public function __construct(MovieRepository $movieRepository, Twig $twig)
    {
        $this->movieRepository = $movieRepository;
        $this->twig = $twig;
    }
    public function onKernelController(ControllerEvent $event): void
    {
        //ici le code qui va s'executer lors de la reception de l'event
        //on va chercher un film au hasard via MovieRepository
        //2 option :
        //option 1 : finAdll() -> random dessus -> pas top pour les perfs
        //option 2 : SQL -> RANDOM() dans la requete sql

        $controller = $event->getController();
        

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (!$controller instanceof MainController) {
            return;
        }
        $randomMovie = $this->movieRepository->findOneRandomMovie();

        $this->twig->addGlobal("randomMovie", $randomMovie);
        

    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
