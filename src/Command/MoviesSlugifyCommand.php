<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Service\MySlugger;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoviesSlugifyCommand extends Command
{
    protected static $defaultName = 'MoviesSlugify';
    protected static $defaultDescription = 'Add a short description for your command';

    private $mySlugger;
    private $movieRepository;
    private $entityManager;

    public function __construct(MovieRepository $movieRepository, MySlugger $mySlugger, ManagerRegistry $doctrine)
    {
        $this->movieRepository = $movieRepository;
        $this->mySlugger = $mySlugger;
        $this->entityManager = $doctrine->getManager();

        parent::__construct();
    }


    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // creer un message styler
        $io = new SymfonyStyle($input, $output);

        $io->info('Mise à jour de nos slug dans la bdd');

        $movies = $this->movieRepository->findAll();
        foreach($movies as $movie)
        {
            $title = $movie->getTitle();
            $movie->setSlug($this->mySlugger->slugify($title));
        }

        $this->entityManager->flush();

        // afficher le message styler en cas de reussite
        $io->success('Les slugs ont bien été mis a jour !');

        return Command::SUCCESS;
    }
}
