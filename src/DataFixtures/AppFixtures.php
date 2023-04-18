<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Season;
use DateTimeImmutable;
use App\Entity\Casting;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Provider\OflixProvider;
use Doctrine\DBAL\Connection;
use App\Service\MySlugger;


class AppFixtures extends Fixture
{
    /**
     * Les propriétés qui vont accueillir les services nécessaires à la classe de Fixtures
     */
    private $connection;
    private $slugger;
    /**
     * On récupère les services utiles via le constructeur
     */
    public function __construct(Connection $connection, MySlugger $slugger)
    {
        // On récupère la connexion à la BDD (DBAL ~= PDO)
        // pour exécuter des requêtes manuelles en SQL pur
        $this->connection = $connection;
        $this->slugger = $slugger;
    }

    /**
     * Permet de TRUNCATE les tables et de remettre les AI à 1
     */
    private function truncate()
    {
        // On passe en mode SQL ! On cause avec MySQL
        // Désactivation la vérification des contraintes FK
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        // On tronque
        $this->connection->executeQuery('TRUNCATE TABLE casting');
        $this->connection->executeQuery('TRUNCATE TABLE genre');
        $this->connection->executeQuery('TRUNCATE TABLE movie');
        $this->connection->executeQuery('TRUNCATE TABLE movie_genre');
        $this->connection->executeQuery('TRUNCATE TABLE person');
        $this->connection->executeQuery('TRUNCATE TABLE review');
        $this->connection->executeQuery('TRUNCATE TABLE season');
        $this->connection->executeQuery('TRUNCATE TABLE user');
        // etc.
    }

    public function load(ObjectManager $manager): void
    {
        // On TRUNCATE manuellement
        $this->truncate();
        // Faker
        $faker = Factory::create('fr_FR');

        // pour avoir toujours les mêmes données (principe de "graine" en pseudo-aléatoire)
        // @see https://fakerphp.github.io/#seeding-the-generator
        $faker->seed(2025);
        // on ajout notre provider à Faker
        $faker->addProvider(new OflixProvider());

    

        
        // Utilisateurs
        
        // admin
        $user = new User();
        $user->setEmail('admin@admin.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('$2y$13$.PJiDK3kq2C4owW5RW6Z3ukzRc14TJZRPcMfXcCy9AyhhA9OMK3Li');
        $manager->persist($user);
        
        // manager
        $user = new User();
        $user->setEmail('manager@manager.com');
        $user->setRoles(['ROLE_MANAGER']);
        $user->setPassword('$2y$13$REdYmAInUfFFy/Bsx9DPb.GiUC9YfRotv3Tt2zqrXpf7sJuTNZMpC');
        $manager->persist($user);

        // user
        $user = new User();
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('$2y$13$CfDM2qlk0uDtA3pv3kbBCOpppYqWUT.EHbws9IkhMQXvBxGfxROv2');
        $manager->persist($user);


        // Genres

        // Tableau pour relation avec les films
        $genresList = [];

        for ($g = 1; $g <= 20; $g++) {

            // Nouveau genre
            $genre = new Genre();
            // un genre unique !
            // @see https://fakerphp.github.io/#modifiers
            $genre->setName($faker->unique()->movieGenre());

            // On l'ajoute à la liste pour usage ultérieur
            $genresList[] = $genre;

            // On persiste
            $manager->persist($genre);
        }

        // Persons

        // Tableau pour nos persons
        $personsList = [];

        for ($i = 1; $i <= 200; $i++) {

            // Nouvelle Person
            $person = new Person();
            $person->setFirstname($faker->firstName());
            $person->setLastname($faker->lastName());

            // On l'ajoute à la liste pour usage ultérieur
            $personsList[] = $person;

            // On persiste
            $manager->persist($person);
        }   

        // Tableau pour nos films
        $moviesList = [];

        for ($m = 1; $m <= 20; $m++) {

            $movie = new Movie();
            $movie->setTitle($faker->unique()->movieTitle());

            // On a 1 chance sur 2 d'avoir un film (sorte de pile ou face)
            $movie->setType($faker->randomElement(['Film', 'Série']));

            $movie->setSummary($faker->text(150));
            $movie->setSynopsis($faker->text(500));

            // on met en place une date aléatoire
            $movie->setReleaseDate($faker->dateTimeBetween('1891-01-01', 'now'));

            $movie->setDuration($faker->numberBetween(30, 263));
            $movie->setPoster('https://picsum.photos/id/' . $faker->numberBetween(1, 100) . '/300/450');
            // Nombre à 1 chiffre après la virgule entre 1 et 5
            $movie->setRating($faker->randomFloat(1, 1, 5)); // 4.2, 3.4, 1.8 etc.

            $movie->setSlug($this->slugger->slugify($movie->getTitle()));

            // les saisons
            // On vérifie si l'entité Movie est une série ou pas
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

            // genres associés
            // on prend genre au hasard dans la liste des genres créées plus haut
            // On ajoute de 1 à 3 films au hasard pour chaque film SANS DOUBLONS
            $randomGenres = $faker->randomElements($genresList, $faker->numberBetween(1, 3));
            foreach ($randomGenres as $genre) {
                $movie->addGenre($genre);
            }

            // Les castings du film

            // On ajoute de 3 à 5 castings par films au hasard pour chaque film
            for ($c = 1; $c <= mt_rand(3, 5); $c++) {

                $casting = new Casting();
                // Les propriétés role et creditOrder
                $casting->setRole($faker->name());
                $casting->setCreditOrder($c);

                // Les 2 associations
                // Movie
                $casting->setMovie($movie);
                // Person
                // On pioche une personne (doublons autorisés !)
                $casting->setPerson($faker->randomElement($personsList));

                // On persiste
                $manager->persist($casting);
            }

            // On ajoute le film à la liste des films
            $moviesList[] = $movie; // = array_push($moviesList, $movie);

            // On persiste
            $manager->persist($movie);
        }

        $manager->flush();
    }
}
