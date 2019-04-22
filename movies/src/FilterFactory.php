<?php namespace MovieAPI;
require '../vendor/autoload.php';

/**
 * Factory class for constructiong a series of filters.
 */
class FilterFactory
{
    private $dataLoader;    // the dataloader
    private $filters;       // an array of filters
    private $result;        // the constructed filters for SQL Query

    /**
     * Constructor for FilterFactory class.
     * 
     * @param $dataLoader Data loader to be used for querys.
     */
    public function __construct($dataLoader)
    {
        $this->dataLoader=$dataLoader;
        $this->buildCategoryFilter();
        $this->buildRatingFilter();
    }

    /**
     * Clear the old filter results. Should be done before starting a new filter.
     */
    public function clearResult()
    {
        $this->result = NULL;
    }

    /**
     * Get the results of the current filter set.
     * 
     * @return string The results of the current filters.
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Create and add a new inclusive category filter 
     * 
     * @param $value The category value to include with the filter
     */
    public function includeCategoryValue($value)
    {
        $this->addNewFilterToResult($this->filters['category']->includeValue($value));
    }
    
    /**
     * Create and add a new inclusive rating filter 
     * 
     * @param $value The rating value to include with the filter
     */
    public function includeRatingValue($value)
    {
        $this->addNewFilterToResult($this->filters['rating']->includeValue($value));
    }

    /**
     * Adds a new filter to the current result.
     * 
     * @param $filter The new filter (in string form) to add.
     */
    private function addNewFilterToResult($filter)
    {
        if ($filter)
        {
            $this->prepResultForNewFilter();
        }
        $this->result .= $filter;
    }

    /**
     * Preps the result string to have a new filter. 
     * Could be expanded in future to include OR and other options.
     */
    private function prepResultForNewFilter()
    {
        if(!$this->result) $this->result = '';
        else $this->result .= ' AND ';
    }

    /**
     * Builds a filter object for film categories.
     * Run before trying to filter by category.
     */
    private function buildCategoryFilter()
    {
        $categoryValues = $this->dataLoader->queryAllCategoryValues();
        $possibleValues = [];

        foreach( $categoryValues as $value ) {
            array_push($possibleValues, array('id'=>$value['category_id'],'value'=>$value['name']));
        }
        //$this->scrubbers = array('category'=>new CategoryScrubber($this->queryAllCategoryValues()));
        $this->filters['category'] = new OptionFilter('category',$possibleValues);
    }

    /**
     * Builds a filter object for film ratings.
     * Run before trying to filter by rating.
     */
    private function buildRatingFilter()
    {
        $ratingValues = $this->dataLoader->queryAllRatingValues();
        $possibleValues = [];
        $counter = 1; // rating doesn't have an actual ID so we'll fake one

        foreach( $ratingValues as $value ) {
            array_push($possibleValues, array('id'=>$counter++,'value'=>$value['rating']));
        }
        $this->filters['rating'] = new OptionFilter('rating',$possibleValues);
    }
}

/**
 * Class to hold an option based Filter. At setup has an initial list of possible values
 * to validate from. Will check and clean filter values against those values.
 */
class OptionFilter
{
    private $valueLabel;
    private $possibleValues;

    /**
     * Constructs the option-based filter.
     * Filter uses a set of known possible values for comparison
     * 
     * @param $valueLabel The label that should be used when creating filter strings. Should match SQL.
     * @param $possibleValues The array of possible values for comparison. Should be a multidimensional array
     *     that includes at least id and value
     */
    public function __construct($valueLabel, $possibleValues)
    {
        $this->valueLabel=$valueLabel;
        $this->possibleValues=$possibleValues;
    }

    /**
     * Constructs a query string segment to include a value.
     * 
     * @param $value The value to include.
     * 
     * @return string The query string to use as a filter
     */
    public function includeValue($value)
    {
        $value = $this->getDatabaseValueFromInputValue($value);
        if(!$value) return false;

        return $this->valueLabel.'=\''.$value.'\'';
    }

    /**
     * Constructs a query string segment to exclude a value.
     * 
     * @param $value The value to exclude.
     * 
     * @return string The query string to use as a filter
     */
    public function excludeValue($value)
    {
        $value = $this->getDatabaseValueFromInputValue($value);
        if(!$value) return false;

        return $this->valueLabel.'<>\''.$value.'\'';
    }

    /**
     * Looks for a known database value that matches provided value.
     * 
     * @param $value The value to check
     * 
     * @return array Associative array that matches the provided value. If no value found then will return false.
     */
    private function getDatabaseValueFromInputValue($value)
    {
        $possibleMatch = $this->searchforMatchingValue($value);
        if ($possibleMatch) return $possibleMatch['value'];
        else return false;
    }

    /**
     * Searches for a known possible value that matches with a provided value.
     * Note: currently linear search, should be improved if expanded
     * 
     * @param $newValue The value to search for
     * 
     * @return array Associative array that matches the provided value. If no value found then will return false.
     */
    private function searchforMatchingValue($newValue)
    {
        foreach( $this->possibleValues as $value ) {
            if(strcasecmp($newValue, $value['value']) == 0)
            {
                return $value;
            }
        }
        return false;
    }

    /**
     * Searches for a known possible value that matches with a provided ID.
     * Note: currently linear search, should be improved if expanded
     * 
     * @param $newID The ID to search for
     * 
     * @return array Associative array that matches the provided value. If no value found then will return false.
     */
    private function searchforMatchingID($newID)
    {
        foreach( $this->possibleValues as $value ) {
            if(strcasecmp($newID, $value['id']) == 0)
            {
                return $value;
            }
        }
        return 0;
    }
}
