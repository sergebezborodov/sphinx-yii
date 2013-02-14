<?php

/**
 * Search criteria for queriing Sphinx
 *
 * Based on mitallast ESphinxCriteria
 */
class ESphinxSearchCriteria extends CComponent
{
    /**
     * @var string fields for select
     */
    public $select = '*';

    /**
     * @var array field filters
     */
    private $_filters = array();

    /**
     * @var array range filters
     */
    private $_rangeFilters = array();

    /**
     * @var string search query
     */
    public $query = '';

    /**
     * @var string target search index
     */
    public $from;

    /**
     * @var int query sorting mode
     * @url http://sphinxsearch.com/docs/manual-2.0.2.html#sorting-modes
     */
    public $sortMode;

    /**
     * @var array sort fields
     */
    private $_orders = array();

    /**
     * @var int
     */
    public $matchMode = 0;

    /**
     * @var int
     */
    public $rankingMode;

    /**
     * @var array fields weights
     */
    private $_weights = array();

    /**
     * @var array group fields
     */
    private $_groupBy = array();

    /**
     * @var string field for distinct group
     */
    public $groupDistinct;

    /**
     * @var int query offset
     */
    public $offset = 0;

    /**
     * @var int resuls limit
     */
    public $limit = 1;

    /**
     * @var int max query result size
     */
    public $maxMatches = 0;

    /**
     * @var int
     */
    public $cutOff = 0;

    /**
     * Init with values
     *
     * @param array $values
     */
    public function __construct($values = array())
    {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Add filter to query
     *
     * @param string $attribute
     * @param int|array $value
     * @param bool $exclude
     * @param string $key filter key, set it if you plan delete the setted filter in future
     */
    public function addFilter($attribute, $value, $exclude = false, $key = null)
    {
        if (!is_string($attribute)) {
            throw new SphinxSearchCriteriaException('Attribute must be a string');
        }

        $value = is_array($value) ? $value : array($value);

        if ($key) {
            $this->_filters[$key] = array(
                'attribute' => $attribute,
                'value'     => $value,
                'exclude'   => (bool)$exclude,
            );
        } else {
            $this->_filters[] = array(
                'attribute' => $attribute,
                'value'     => $value,
                'exclude'   => (bool)$exclude,
            );
        }
    }

    /**
     * Adds filters to query
     * example:
     *      array(
     *          array('attr1', array(1,2,3,4), 'exclude' => true, 'key' => 'attr1')
     *          array('attr1', array(1,2,3,4), 'exclude' => true)
     *          array('attr1', array(1,2,3,4))
     *          array('attr1', 1)
     *      )
     *
     * @param array $filters filters array
     */
    public function addFilters($filters)
    {
        if (empty($filters) || !is_array($filters)) {
            throw new SphinxSearchCriteriaException('Filters must be a non empty array');
        }

        foreach ($filters as $filter) {
            $attribute = $filter[0];
            $value     = is_array($filter[1]) ? $filter[1] : array($filter[1]);
            $exclude   = !empty($filter['exclude']);
            $key       = !empty($filter['key']) ? $filter['key'] : null;

            $this->addFilter($attribute, $value, $exclude, $key);
        }
    }

    /**
     * Sets new filters. Old filters will be cleaned
     *
     * @param array $filters
     */
    public function setFilters($filters)
    {
        $this->resetFilters();
        $this->addFilters($filters);
    }

    /**
     * Clean all filters
     */
    public function resetFilters()
    {
        $this->_filters = array();
    }

    /**
     * Remove filter value by key
     *
     * @param string $key
     */
    public function deleteFilter($key)
    {
        unset($this->_filters[$key]);
    }

    /**
     * @return array return all exists filter
     */
    public function getFilters()
    {
        return $this->_filters;
    }
}
