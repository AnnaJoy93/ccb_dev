<?php namespace MovieAPI\Test;
require '../vendor/autoload.php';

class DataLoaderTest 
{
    private $db;
    private $filterFactory;
    private $dataloader;
    private $failure = 0;
    
    public function __construct($dataLoader) 
    {
        // $this->db = $db;
        $this->dataloader = $dataLoader;
    }

    public function runAllTests()
    {
        $result = [];

        // ------ TEST: queryMovies ------
        array_push($result, $this->testQueryMoviesParameters());
        array_push($result, $this->testQueryMoviesChangeTitle());
        array_push($result, $this->testQueryMoviesChangeCategory());
        array_push($result, $this->testQueryMoviesChangeRating());
        array_push($result, $this->testQueryMoviesBadCategory());
        array_push($result, $this->testQueryMoviesBadRating());
        array_push($result, $this->testQueryMoviesWorthlessTitleSearch());

        // ------ TEST: queryActorsByFilmID ------
        array_push($result, $this->testQueryActorsChangeWithFilmID());
        array_push($result, $this->testQueryActorsWithInvalidId());
        array_push($result, $this->testQueryActorsWithNullId());

        // ------ TEST: queryDetailsByFilmID ------
        array_push($result, $this->testQueryDetailsChangeWithFilmID());
        array_push($result, $this->testQueryDetailsWithInvalidId());
        array_push($result, $this->testQueryDetailsWithNullId());


        return $result;
    }


    // ------ TEST: queryMovies ------

    public function testQueryMoviesParameters()
    {
        $description = 'test queryMovies: all parameters work';
        $expected = array(["FID"=>101,"title"=>"BROTHERHOOD BLANKET","category"=>"Documentary","rating"=>"R"]);
        $recieved = $this->dataloader->queryMovies('BROTHER', 'Documentary', 'r');

        return array(
            'description'=>$description,
            'success'=>($expected==$recieved));
    }

    public function testQueryMoviesChangeTitle()
    {
        $description = 'test queryMovies: changing title changes results';
        $expected = $this->dataloader->queryMovies('BROTHER', 'Documentary', 'r');
        $recieved = $this->dataloader->queryMovies('HI', 'Documentary', 'r');

        return array(
            'description'=>$description,
            'success'=>($expected!=$recieved));
    }

    public function testQueryMoviesChangeCategory()
    {
        $description = 'test queryMovies: changing category changes results';
        $expected = $this->dataloader->queryMovies('BROTHER', 'Documentary', 'r');
        $recieved = $this->dataloader->queryMovies('BROTHER', 'Action', 'r');

        return array(
            'description'=>$description,
            'success'=>($expected!=$recieved));
    }

    public function testQueryMoviesChangeRating()
    {
        $description = 'test queryMovies: changing rating changes results';
        $expected = $this->dataloader->queryMovies('BROTHER', 'Documentary', 'r');
        $recieved = $this->dataloader->queryMovies('BROTHER', 'Documentary', 'pg');

        return array(
            'description'=>$description,
            'success'=>($expected!=$recieved));
    }

    public function testQueryMoviesBadCategory()
    {
        $description = 'test queryMovies: bad category value returns the same as null';
        $expected = $this->dataloader->queryMovies('BROTHER', NULL, 'r');
        $recieved = $this->dataloader->queryMovies('BROTHER', 'bad', 'r');

        return array(
            'description'=>$description,
            'success'=>($expected==$recieved));
    }

    public function testQueryMoviesBadRating()
    {
        $description = 'test queryMovies: bad rating value returns the same as null';
        $expected = $this->dataloader->queryMovies('BROTHER', 'Documentary', NULL);
        $recieved = $this->dataloader->queryMovies('BROTHER', 'Documentary', 'bad');

        return array(
            'description'=>$description,
            'success'=>($expected==$recieved));
    }

    public function testQueryMoviesWorthlessTitleSearch()
    {
        $description = 'test queryMovies: worthless title search returns nothing';
        $expected = array();
        $recieved = $this->dataloader->queryMovies('lj;lkjasdfkaefiheuin', NULL, NULL);

        return array(
            'description'=>$description,
            'success'=>($expected==$recieved));
    }


    // ------ TEST: queryActorsByFilmID ------

    public function testQueryActorsChangeWithFilmID()
    {
        $description = 'test queryActorsByFilmID: actors change if film ID does';
        $expected = $this->dataloader->queryActorsByFilmID(1);
        $recieved = $this->dataloader->queryActorsByFilmID(11);

        return array(
            'description'=>$description,
            'success'=>($expected!=$recieved));
    }

    public function testQueryActorsWithInvalidId()
    {
        $description = 'test queryActorsByFilmID: returns nothing if film id is invalid';
        $expected = array();
        $recieved = $this->dataloader->queryActorsByFilmID(notanid);

        return array(
            'description'=>$description,
            'success'=>($expected==$recieved));
    }

    public function testQueryActorsWithNullId()
    {
        $description = 'test queryActorsByFilmID: returns nothing if film id is NULL';
        $expected = array();
        $recieved = $this->dataloader->queryActorsByFilmID(NULL);

        return array(
            'description'=>$description,
            'success'=>($expected==$recieved));
    }


        // ------ TEST: queryDetailsByFilmID ------

        public function testQueryDetailsChangeWithFilmID()
        {
            $description = 'test queryDetailsByFilmID: details change if film ID does';
            $expected = $this->dataloader->queryDetailsByFilmID(1);
            $recieved = $this->dataloader->queryDetailsByFilmID(11);
    
            return array(
                'description'=>$description,
                'success'=>($expected!=$recieved));
        }
    
        public function testQueryDetailsWithInvalidId()
        {
            $description = 'test queryDetailsByFilmID: returns nothing if film id is invalid';
            $expected = array();
            $recieved = $this->dataloader->queryDetailsByFilmID(notanid);
    
            return array(
                'description'=>$description,
                'success'=>($expected==$recieved));
        }
    
        public function testQueryDetailsWithNullId()
        {
            $description = 'test queryDetailsByFilmID: returns nothing if film id is NULL';
            $expected = array();
            $recieved = $this->dataloader->queryDetailsByFilmID(NULL);
    
            return array(
                'description'=>$description,
                'success'=>($expected==$recieved));
        }
    
}