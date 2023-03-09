<?php

namespace App\DataFixtures;

use App\Entity\Casting;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Season;
use DateTime;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PhpParser\Node\Expr\Cast\Array_;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
    $movies = [];
    $persons = [];

    for ($i = 1; $i <= 50; $i++) {
        $person = new Person();
        $faker = Factory::create();
        $person->setFirstname($faker->firstName());
        $person->setLastname($faker->lastName());
        $manager->persist($person);
        array_push($persons, $person);
    }

    $genreComedie = new Genre();
    $genreComedie->setName('Comédie');
    $manager->persist($genreComedie);

    $genreThriller = new Genre();
    $genreThriller->setName('Thriller');
    $manager->persist($genreThriller);

    $genreHorreur = new Genre();
    $genreHorreur->setName('Horreur');
    $manager->persist($genreHorreur);

    $genreDocumentaire = new Genre();
    $genreDocumentaire->setName('Documentaire');
    $manager->persist($genreDocumentaire);

    $genreAction = new Genre();
    $genreAction->setName('Action');
    $manager->persist($genreAction);

    $genreLove = new Genre();
    $genreLove->setName('Love');
    $manager->persist($genreLove);

    $genres = [$genreAction, $genreComedie, $genreDocumentaire, $genreHorreur, $genreLove];

    $types = array("Film", "Série");



    for ($i = 1; $i <= 50; $i++) 
    {
        $movie = new Movie();
        $faker = Factory::create();
        $movie->setTitle($faker->sentence());
        $movie->setDuration($faker->numberBetween(1, 300));
        $movie->setSummary($faker->text(100));
        $movie->setSynopsis($faker->text(100));
        $movie->setPoster('https://picsum.photos/id/' . mt_rand(1, 100) . '/450/300');
        $movie->setReleaseDate($faker->DateTime());
        $movie->setType($types[array_rand($types)]);

        foreach ($genres as $genre) {
            $movie->addGenre($genre);
        }

        $movie->setRating($faker->randomFloat(1, 0, 5));

        if ($movie->getType() === 'Série') {
            // Si oui on crée une bouble for avec un numéro aléatoire dans la condition pour déterminer le nombre de saisons
            // mt_rand() ne sera exécuté qu'une fois en début de boucle
            for ($s = 1; $s <= mt_rand(1, 8); $s++) {
                // On créé la nouvelle entité Season
                $season = new Season();
                // On insert le numéro de la saison en cours $s
                $season->setNumber($s);
                // On insert un numéro d'épisode aléatoire
                $season->setEpisodesNumber(mt_rand(6, 24));
                // Puis on relie notre saison à notre série
                $season->setMovie($movie);
                // On persite
                $manager->persist($season);
            }
        }

        for ($c = 1; $c <= 5; $c++) {
            $casting = new Casting();
            $faker = Factory::create();
            $casting->setRole($faker->word());
            $casting->setMovie($movie);
            $casting->setPerson($persons[array_rand($persons)]);
            $manager->persist($casting);
        }


        $manager->persist($movie);
        array_push($movies, $movie);
    }



        $manager->flush();
    }
}
