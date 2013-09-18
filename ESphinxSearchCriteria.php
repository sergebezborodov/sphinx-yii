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
     * @url http://sphinxsearch.com/docs/current.html#sphinxql-select
     * @url http://sphinxsearch.com/docs/current.html#api-func-query
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
     * @var int query sorting mode
     * @url http://sphinxsearch.com/docs/current.html#api-func-setsortmode
     */
    public $sortMode;

    /**
     * @var string
     */
    private $_sortBy = "";

    /**
     * @var array sort fields
     */
    private $_orders = array();

    /**
     * @var int
     * @url http://sphinxsearch.com/docs/current.html#matching-modes
     */
    public $matchMode = ESphinxMatch::ALL;

    /**
     * @var int
     *
     */
    public $rankingMode;

    /**
     * String expression only for SPH_RANK_EXPR mode
     *
     * @var string
     */
    public $rankingExpression = '';

    /**
     * @var array fields weights
     */
    private $_fieldWeights = array();

    /**
     * @var array index weights
     */
    private $_indexWeights = array();

    /**
     * Group by field
     *
     * @var string|null
     */
    public $groupBy = null;

    /**
     * Group by function, default by attr
     *
     * @var int
     */
    public $groupByFunc = ESphinxGroup::BY_ATTR;

    /**
     * Groups sorting, refers to ORDER BY in sql mode
     *
     * @var string
     */
    public $groupBySort = '@groupby desc';

    /**
     * @var string field for distinct group
     */
    public $groupDistinct;

    private $_minId;
    private $_maxId;

    /**
     * @var int query offset
     */
    public $offset = 0;

    /**
     * @var int resuls limit
     */
    public $limit = 20;

    /**
     * @var int max query result size
     */
    public $maxMatches;

    /**
     * @var int
     */
    public $cutOff;

    /**
     * User defined comment for query, copied verbatim to query log
     *
     * @var string
     */
    public $comment;

    /**
     * Since version 2.1.1-beta, queries may be automatically optimized if this flag is true
     *
     * @var bool
     */
    public $booleanSimplify;

    /**
     * @var int
     */
    private $_reverseScan;

    /**
     * @var string
     */
    private $_sortMethod;

    /**
     * Use global statistics (frequencies) from the global_idf file for IDF computations,
     * rather than the local index statistics
     *
     * @var string
     */
    public $globalIdf;

    /**
     * The standard IDF (Inverse Document Frequency) calculation may cause undesired keyword
     * penalization effects in the BM25 weighting functions.
     *
     * @var string
     */
    private $_idf;


    /**
     * Query timeout in ms
     * If not specified will use connection parametrs
     *
     * @var int|null
     */
    public $queryTimeout;


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
            throw new ESphinxException('Attribute must be a string');
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
            throw new ESphinxException('Filters must be a non empty array');
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
        $this->cleanFilters();
        $this->addFilters($filters);
    }

    /**
     * Clean all filters
     */
    public function cleanFilters()
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

    /**
     * Adds a range filter <br/>
     * Values can be integer or float
     *
     * @param string $attribute
     * @param int|float $min
     * @param int|float $max
     * @param string $key
     * @param bool $exclude
     */
    public function addRangeFilter($attribute, $min, $max, $exclude = false, $key = null)
    {
        if (empty($attribute) || !is_string($attribute) || !is_numeric($min) || !is_numeric($max)) {
            throw new ESphinxException('Check input data');
        }

        $float = is_float($min) || is_float($max);
        if ($float) {
            $min = (float)$min;
            $max = (float)$max;
        } else {
            $min = (int)$min;
            $max = (int)$max;
        }

        if ($key) {
            $this->_rangeFilters[$key] = array(
                'attribute' => $attribute,
                'min'       => $min,
                'max'       => $max,
                'exclude'   => $exclude,
                'float'     => $float,
            );
        } else {
            $this->_rangeFilters[] = array(
                'attribute' => $attribute,
                'min'       => $min,
                'max'       => $max,
                'exclude'   => $exclude,
                'float'     => $float,
            );
        }
    }

    /**
     *
     * Adds group range filters
     * example:
     *      array(
     *          array('attribute', 'min' => 0, 'max' => 100, 'exclude' => true, 'key' => 'attr') <br>
     *          array('attribute', 'min' => 0, 'max' => 100, 'exclude' => true) <br>
     *          array('attribute', 'min' => 0, 'max' => 100) <br>
     *      )
     *
     * @param array $rangeFilters
     */
    public function addRangeFilters($rangeFilters)
    {
        if (empty($rangeFilters)) {
            throw new ESphinxException('Filters must be a non empty array');
        }

        foreach ($rangeFilters as $rangeFilter) {
            if (empty($rangeFilter) || count($rangeFilter) < 3
                || !isset($rangeFilter[0], $rangeFilter['min'], $rangeFilter['max'])) {
                throw new ESphinxException('Check input data');
            }
            $attribute = $rangeFilter[0];
            $exclude   = !empty($rangeFilter['exclude']);
            $key       = !empty($rangeFilter['key']) ? $rangeFilter['key'] : null;

            $this->addRangeFilter($attribute, $rangeFilter['min'], $rangeFilter['max'], $exclude, $key);
        }
    }

    /**
     * Sets new range filters, old filters will be removed
     *
     * @see addRangeFilters
     * @param $rangeFilters
     */
    public function setRangeFilters($rangeFilters) {
        $this->cleanRangeFilters();
        $this->addRangeFilters($rangeFilters);
    }

    /**
     * Clean range filters
     */
    public function cleanRangeFilters()
    {
        $this->_rangeFilters = array();
    }

    /**
     * @return array
     */
    public function getRangeFilters()
    {
        return $this->_rangeFilters;
    }

    /**
     * Remove range filter by key
     *
     * @param string $key
     */
    public function deleteRangeFilter($key)
    {
        unset($this->_rangeFilters[$key]);
    }


    /**
     * @return string
     */
    public function getSortBy()
    {
        if ($this->sortMode == ESphinxSort::EXTENDED) {
            throw new ESphinxException('Use getSortBy is not allowen with EXTENDED sort mode, use getOrders');
        }

        return $this->_sortBy;
    }

    /**
     * Sets sort for all modes, except EXTENDED
     *
     * @param string $value
     */
    public function setSortBy($value)
    {
        if ($this->sortMode == ESphinxSort::EXTENDED) {
            throw new ESphinxException('Use setSortBy is not allowen with EXTENDED sort mode, use getOrders');
        }

        $this->_sortBy = $value;
    }

    private function checkIsExtendedOrderMode()
    {
        if ($this->sortMode != ESphinxSort::EXTENDED) {
            throw new ESphinxException('addOrder method can uses only with EXTENDED sort mode, '
                .'for other use sortBy property');
        }
    }

    /**
     * Add sort order
     *
     * @param string $attribute
     * @param string $order asc|desc
     */
    public function addOrder($attribute, $order)
    {
        $this->checkIsExtendedOrderMode();
        $order = strtoupper($order);
        if ($order != 'ASC' && $order != 'DESC') {
            throw new ESphinxException('Invalid value for sorting direction');
        }

        $this->_orders[$attribute] = $order;
    }

    /**
     * Adds sorting group <br>
     * example: <br>
     *      array( <br>
     *          'field1' => 'asc', <br>
     *          'field2' => 'DESC' <br>
     *      ) <br>
     * @param array $orders
     */
    public function addOrders($orders)
    {
        $this->checkIsExtendedOrderMode();

        if (empty($orders) || !is_array($orders)) {
            throw new ESphinxException('Orders must be a non empty array');
        }

        foreach ($orders as $field => $value) {
            $this->addOrder($field, $value);
        }
    }

    /**
     * @param array $orders
     */
    public function setOrders($orders)
    {
        $this->checkIsExtendedOrderMode();
        $this->cleanOrders();
        $this->addOrders($orders);
    }

    /**
     * Clean sort orders
     */
    public function cleanOrders()
    {
        $this->checkIsExtendedOrderMode();
        $this->_orders = array();
    }

    /**
     * @return array
     */
    public function getOrders()
    {
        $this->checkIsExtendedOrderMode();
        return $this->_orders;
    }

    /**
     * Add field weights
     *
     * @param string $field
     * @param int $weight
     */
    public function addFieldWeight($field, $weight)
    {
        if (!is_integer($weight)) {
            throw new ESphinxException('Field weight must be integer');
        }

        $this->_fieldWeights[$field] = $weight;
    }

    /**
     * Add group fieds weights <br/>
     *      array( <br>
     *          'field1' => 5, <br>
     *          'field2' => 1, <br>
     *      ) <br>
     *
     * @param array $weights
     */
    public function addFieldWeights($weights)
    {
        if (empty($weights) || !is_array($weights)) {
            throw new ESphinxException('Weights must be a non empty array');
        }

        foreach ($weights as $field => $value) {
            $this->addFieldWeight($field, $value);
        }
    }

    /**
     * Sets new field weights values, old values will be removed
     *
     * @param array $weights
     */
    public function setFieldWeights($weights)
    {
        $this->cleanFieldWeights();
        $this->addFieldWeights($weights);
    }

    /**
     * Clean field weights
     */
    public function cleanFieldWeights()
    {
        $this->_fieldWeights = array();
    }

    /**
     * @return array
     */
    public function getFieldWeights()
    {
        return $this->_fieldWeights;
    }


    /**
     * Add index weights
     *
     * @param string $index
     * @param int $weight
     */
    public function addIndexWeight($index, $weight)
    {
        if (!is_integer($weight)) {
            throw new ESphinxException('Index weight must be integer');
        }

        $this->_indexWeights[$index] = $weight;
    }

    /**
     * Add group index weights <br/>
     *      array( <br>
     *          'index1' => 5, <br>
     *          'index2' => 1, <br>
     *      ) <br>
     *
     * @param array $weights
     */
    public function addIndexWeights($weights)
    {
        if (empty($weights) || !is_array($weights)) {
            throw new ESphinxException('Weights must be a non empty array');
        }

        foreach ($weights as $field => $value) {
            $this->addIndexWeight($field, $value);
        }
    }

    /**
     * Sets new field weights values, old values will be removed
     *
     * @param array $weights
     */
    public function setIndexWeights($weights)
    {
        $this->cleanIndexWeights();
        $this->addIndexWeights($weights);
    }

    /**
     * Clean field weights
     */
    public function cleanIndexWeights()
    {
        $this->_indexWeights = array();
    }

    /**
     * @return array
     */
    public function getIndexWeights()
    {
        return $this->_indexWeights;
    }

    /**
     * Check is limit setted.
     *
     * @return bool true if is limited
     */
    public function getIsLimited()
    {
        return (int)$this->limit > 0;
    }


    /**
     * Set filter by model id range
     * @param int $min
     * @param int $max
     * @see getIdMax
     * @see getMinId
     */
    public function setIdRange($min, $max)
    {
        $this->_minId = (int)$min;
        $this->_maxId = (int)$max;
    }


    /**
     * Get maximum id in range
     *
     * @return int
     * @see getIdMax
     * @see setIdRange
     */
    public function getMaxId()
    {
        return $this->_maxId;
    }

    /**
     * Set max id value
     *
     * @param int $max
     */
    public function setMaxId($max)
    {
        $this->_maxId = intval($max);
    }

    /**
     * Get minimum id in range
     *
     * @return int
     * @see getMinId
     * @see setIdRange
     */
    public function getMinId()
    {
        return $this->_minId;
    }


    /**
     * Set min id value
     *
     * @param int $min
     */
    public function setMinId($min)
    {
        $this->_minId = intval($min);
    }

    /**
     * Check is id range setted
     * @return bool
     */
    public function getIsIdRangeSetted()
    {
        return is_int($this->_minId) && is_int($this->_maxId);
    }

    /**
     * @return int
     */
    public function getReverseScan()
    {
        return $this->_reverseScan;
    }

    /**
     * Lets you control the order in which full-scan query processes the rows
     *
     * @param bool $reverseScan
     */
    public function setReverseScan($reverseScan)
    {
        $this->_reverseScan = (bool)$reverseScan;
    }

    /**
     * @return string
     */
    public function getSortMethod()
    {
        return $this->_sortMethod;
    }

    /**
     * 'pq' (priority queue, set by default) or 'kbuffer'
     * (gives faster sorting for already pre-sorted data, e.g. index data sorted by id).
     *
     * @param string $sortMethod
     */
    public function setSortMethod($sortMethod)
    {
        if ($sortMethod != 'pq' && $sortMethod != 'kbuffer') {
            throw new ESphinxException('Invalid value for sortMethod');
        }

        $this->_sortMethod = $sortMethod;
    }

    /**
     * @return string
     */
    public function getIdf()
    {
        return $this->_idf;
    }

    /**
     * The standard IDF (Inverse Document Frequency) calculation may cause undesired keyword
     * penalization effects in the BM25 weighting functions.
     * Either 'normalized' (default) or 'plain'
     *
     * @param string $idf
     */
    public function setIdf($idf)
    {
        if ($idf != 'normalized' && $idf != 'plain') {
            throw new ESphinxException('Invalid value for idf flag');
        }

        $this->_idf = $idf;
    }
}
