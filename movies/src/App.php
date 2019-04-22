<?php namespace MovieAPI;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PDO;

require '../vendor/autoload.php';

class App
{
    /**
     * Stores an instance of the Slim application.
     *
     * @var \Slim\App
     */
    private $app;

    public function __construct() {
        $config['displayErrorDetails'] = true; // helpful for debug - TEMP

        // DB config
        $config['db']['host']   = 'mysql';
        $config['db']['user']   = 'sakila';
        $config['db']['pass']   = 'sakila';
        $config['db']['dbname'] = 'sakila';

        // make the thing
        $app = new \Slim\App(['settings' => $config]);

        // DB/PDO setup
        $container = $app->getContainer();
        $container['db'] = function ($c)
        {
            $db = $c['settings']['db'];
            $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
                $db['user'], $db['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $pdo;
        };

        // make data loader - should be singleton
        $dataLoader = new DataLoader($container->get('db'));

        // ----- APPLICATION ROUTES -----
        $app->get('/movies', function (Request $request, Response $response, array $args) use ($dataLoader)
        {
            // gets a list of movies
            // accepts params title(search), category (inclusive filter), rating (inclusive filter)
            // will ignore params that are not included

            $title = $request->getParam('title');
            $category = $request->getParam('category');
            $rating = $request->getParam('rating');

            $result = $dataLoader->queryMovies($title, $category, $rating);
 
            return $response->withJson($result);
        });

        $app->get('/movies/details', function (Request $request, Response $response, array $args) use ($dataLoader)
        {
            // gets details for a single movie by ID
            // accepts params film_id

            $filmID = $request->getParam('film_id');
            if (!$filmID) $result = NULL;
            else $result = $dataLoader->queryDetailsByFilmID($filmID);

            return $response->withJson($result);
        });


        $app->get('/actors', function (Request $request, Response $response, array $args) use ($dataLoader)
        {
            // gets actors for a single movie by ID
            // accepts params film_id

            $filmID = $request->getParam('film_id');
            if (!$filmID) $result = NULL;
            else $result = $dataLoader->queryActorsByFilmID($filmID);

            return $response->withJson($result);
        });


        // ----- TESTING ROUTES - TEMP -----
        $app->get('/test/dataloader', function (Request $request, Response $response, array $args) use ($dataLoader)
        {
            // runs some tests against the DataLoader class

            $tester = new \MovieAPI\Test\DataLoaderTest($dataLoader);

            return $response->withJson($tester->runAllTests());
        });

        $this->app = $app;
    }
    /**
     * Get an instance of the application.
     *
     * @return \Slim\App
     */
    public function get()
    {
        return $this->app;
    }
}
