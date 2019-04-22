<?php namespace MovieAPI;
require '../vendor/autoload.php';

class DataLoader 
{
    private $db;
    private $filterFactory;
    
    /**
     * Constructor for DataLoader class. 
     * 
     * @param $db The database container to use for connections.
     */
    public function __construct($db) {
        $this->db = $db;
        $this->filterFactory = new FilterFactory($this);
    }

    /**
     * Search and filter movie list.
     * 
     * @param $title String to be used in title search. Not optional but can be NULL.
     * @param $category String to use for category filter. Not optional but can be NULL.
     * @param $rating String to use for rating filter. Not optional but can be NULL.
     * 
     * @return array An array of movies from query.
     */
    public function queryMovies($title, $category, $rating)
    {
        $sqlBase = 'SELECT FID, title, category, rating FROM film_list';
        $sqlExtend = [];
        $parameters = array();

        if($title) 
        {
            //$sqlTitle .= 'title LIKE :title';
            array_push($sqlExtend, 'title LIKE :title');
            $parameters[':title'] = '%'.$title.'%';

            if($category || $rating) $sql .= ' AND ';
        }

        if ($category || $rating)
        {
            $this->filterFactory->clearResult();

            $this->filterFactory->includeCategoryValue($category);
            $this->filterFactory->includeRatingValue($rating);

            array_push($sqlExtend, $this->filterFactory->getResult());
        }

        $sql = $this->assembleStatement($sqlBase, $sqlExtend);

        return $this->queryBase($sql, $parameters);
    }
    

    /**
     * Get a list of actors in a specific movie
     * 
     * @param $filmID The ID of the movie to get actors for
     * 
     * @return array An array of actors from query.
     */
    public function queryActorsByFilmID($filmID)
    {
        $sql = 'SELECT film_actor.film_id, actor.actor_id, actor.first_name, actor.last_name 
        FROM film_actor LEFT JOIN actor ON film_actor.actor_id=actor.actor_id 
        WHERE film_actor.film_id=:filmID';
        $parameters[':filmID'] = $filmID;

        return $this->queryBase($sql, $parameters);
    }

    /**
     * Get a list of movie details for a specific movie
     * 
     * @param $filmID The ID of the movie to get details for
     * 
     * @return array An array of details from query.
     */
    public function queryDetailsByFilmID($filmID)
    {
        $sql = 'SELECT film.film_id, film.title, film.release_year, language.name  AS language,
        original_language.name  AS original_language, film.rental_duration, film.rental_rate,
        film.length, film.replacement_cost, film.rating, film.special_features, film.last_update,
        film.description
        FROM film 
        LEFT JOIN language AS language ON film.language_id=language.language_id 
        LEFT JOIN language AS original_language ON film.original_language_id=language.language_id
        WHERE film.film_id=:filmID';
        $parameters[':filmID'] = $filmID;

        return $this->queryBase($sql, $parameters);
    }

    /**
     * Get a list of all category values in database
     * 
     * @return array An array of category values from query.
     */
    public function queryAllCategoryValues()
    {
        $sql = 'SELECT category_id, name FROM category';
        return $this->queryBase($sql);
    }

    /**
     * Get a list of all unique rating values in database
     * 
     * @return array An array of rating values from query.
     */
    public function queryAllRatingValues()
    {
        $sql = 'SELECT DISTINCT rating FROM film_list';
        return $this->queryBase($sql);
    }

    /**
     * Base method for running database queries
     * 
     * @param $sql The SQL string to use for the query
     * @param $executionParameters The execution parameters for the query, can be null or optional
     * 
     * @return array An array of results from query.
     */
    private function queryBase($sql, $executionParameters = null)
    {
        $query = $this->db->prepare($sql);
        $query->execute($executionParameters);
        return $query->fetchAll();
    }

    /**
     * Assembles a query statement into one.
     * 
     * @param $base The base string for the query (ex. "select ...")
     * @param $extentions Any extentsions, in order, that should be added to the query base
     * 
     * @return string The new query string
     */
    private function assembleStatement($base, $extentions = null)
    {
        $sqlExtend;
        if ($extentions)
        {
            foreach($extentions as $x)
            {
                if(!$sqlExtend) $sqlExtend = ' WHERE ';
                else $sqlExtend .= ' AND ';

                $sqlExtend .= $x;
            }
        }

        return $base.$sqlExtend;
    }
}