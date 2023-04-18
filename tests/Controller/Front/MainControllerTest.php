<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class MainControllerTest extends WebTestCase
{
    /*
     * Home
     */
    public function testHome(): void
    {
        // On créer un client
        $client = static::createClient();

        // On execute une requete HTTP en methode GET sur l'URL '/'
        $crawler = $client->request('GET', '/');

        // On check si on a un status code compris entre 200 et 299 (donc que la requete s'est bien passée)
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Films, séries TV et popcorn en illimité.');
    }

    /*
    * Movie Show
    */
    public function testMovieShow(): void
    {
        // On créer un client
        $client = static::createClient();

        // On execute une requete HTTP en methode GET sur l'URL '/'
        $crawler = $client->request('GET', '/movie/show/freaky-friday');

        // On check si on a un status code compris entre 200 et 299 (donc que la requete s'est bien passée)
        $this->assertResponseIsSuccessful();
    }

    public function testReviewAdd(): void
    {
        // On créer un client
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('user@user.com');

        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/movie/15/review/add');

        // on recupere le formulaire pour inserer un commentaire, le name du form est review et filter me permet de recuperer le contenu de la balise qui a name="review" 
        $form = $crawler->filter('form[name="review"]')->form();

        $form['review[username]'] = "tototata";
        $form['review[email]'] = "imed@imed.com";
        $form['review[content]'] = "contencontenconten";
        $form['review[rating]'] = 5;
        $form['review[watchedAt][month]'] = 2;
        $form['review[watchedAt][day]'] = 23;
        $form['review[watchedAt][year]'] = 2023;

        $form['review[reactions]'] = ["rire"];
        // $form['review[reactions]']


        $client->submit($form);
        $this->assertResponseStatusCodeSame(302);
    }

    public function testAddMovieList(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('user@user.com');

        $client->loginUser($testUser);

        $client->request('POST', '/favorites/add/20');


        $this->assertResponseStatusCodeSame(302);


    }

    /**
     * Test de routes en method POST
     *
     * @dataProvider postUrls
     */
    public function testPostUrls($url): void
    {
        $client = static::createClient();
        $crawler = $client->request('POST', $url);

        $this->assertResponseStatusCodeSame(302);
    }

    public function postUrls()
    {
        // yield est un générateur qui permet d'envoyer des urls, toutes nos url dans l'argument dans le prototype de la methode qui apelle les urls, ne pas oublier @dataProvider postUrls
        yield['/back/movie/1/edit'];
        yield['/back/movie/new'];
        yield['/back/user/new'];
        yield['/back/user/1/edit'];
    }


}
