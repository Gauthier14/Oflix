<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Service\OmdbApi;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoviesPosterCommand extends Command
{
    protected static $defaultName = 'MoviesPosterCommand';
    protected static $defaultDescription = 'Add a short description for your command';

    private $movieRepository;
    private $omdbApi;
    private $managerRegistry;

    public function __construct(MovieRepository $movieRepository, OmdbApi $omdbApi, ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry->getManager();
        $this->omdbApi = $omdbApi;
        $this->movieRepository = $movieRepository;

        parent ::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Mise à jour des posters');
        $movies = $this->movieRepository->findAll();


        foreach($movies as $movie)
        {
            $moviePoster = $this->omdbApi->fetchPoster($movie->getTitle());
            if(!$moviePoster) 
            {
                $io->warning('Poster non trouvé');
            }
            $movie->setPoster($moviePoster);
        }

        $this->managerRegistry->flush();




        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
