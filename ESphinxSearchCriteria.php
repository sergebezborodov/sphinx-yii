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
            throw new SphinxSearchCriteriaException('Check input data');
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
            throw new SphinxSearchCriteriaException('Filters must be a non empty array');
        }

        foreach ($rangeFilters as $rangeFilter) {
            if (empty($rangeFilter) || count($rangeFilter) < 3
                || !isset($rangeFilter[0], $rangeFilter['min'], $rangeFilter['max'])) {
                throw new SphinxSearchCriteriaException('Check input data');
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
     * Add sort order
     *
     * @param string $attribute
     * @param string $order asc|desc
     */
    public function addOrder($attribute, $order)
    {
        // TODO: sorting for another sort modes!!!
        $order = strtoupper($order);
        if ($order != 'ASC' && $order != 'DESC') {
            throw new SphinxSearchCriteriaException('Invalid value for sorting direction');
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
        if (empty($orders) || !is_array($orders)) {
            throw new SphinxSearchCriteriaException('Orders must be a non empty array');
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
        $this->cleanOrders();
        $this->addOrders($orders);
    }

    /**
     * Clean sort orders
     */
    public function cleanOrders()
    {
        $this->_orders = array();
    }

    /**
     * @return array
     */
    public function getOrders()
    {
        return $this->_orders;
    }



    /**
     * Add field weights
     *
     * @param string $field
     * @param int $weight
     */
    public function addWeght($field, $weight)
    {
        if (!is_integer($weight)) {
            throw new SphinxSearchCriteriaException('Field weight must be integer');
        }

        $this->_weights[$field] = $weight;
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
    public function addWeights($weights)
    {
        if (empty($weights) || !is_array($weights)) {
            throw new SphinxSearchCriteriaException('Weights must be a non empty array');
        }

        foreach ($weights as $field => $value) {
            $this->addWeght($field, $value);
        }
    }

    /**
     * Sets new field weights values, old values will be removed
     *
     * @param array $weights
     */
    public function setWeights($weights)
    {
        $this->cleanWeights();
        $this->addWeights($weights);
    }

    /**
     * Clean field weights
     */
    public function cleanWeights()
    {
        $this->_weights = array();
    }

    /**
     * @return array
     */
    public function getWeights()
    {
        return $this->_weights;
    }

    /**
     * Add group by field
     *
     * @param string $attribute
     * @param string $func
     * @param string|null $groupSort
     */
    public function addGroupBy($attribute, $func, $groupSort = null)
    {
        $this->_groupBy[] = array(
            'attribute' => $attribute,
            'value'     => $func,
            'groupSort' => $groupSort,
        );
    }

    /**
     * Add groups by fields
     *      //     attr           func            group sort
     *      array('attribute', SPH_GROUPBY_ATTR, '@group desc'),
     *      array('attribute', SPH_GROUPBY_ATTR),
     *
     * @param array $values
     */
    public function addGroupBys($values) {
        if (empty($values) || !is_array($values)) {
            throw new SphinxSearchCriteriaException('Groups must be a non empty array');
        }

        foreach ($values as $value) {
            if (!is_array($value) || count($value) < 2) {
                throw new SphinxSearchCriteriaException('Invalid value for field');
            }
            $this->addGroupBy($value[0], $value[1], isset($value[2]) ? $value[2] : null);
        }
    }

    /**
     * Sets group func values, old values will be removed
     *
     * @param array $groupBy
     */
    public function setGroupBys($groupBy)
    {
        $this->cleanGroupBy();
        $this->addGroupBys($groupBy);
    }

    /**
     * Clean group func values
     */
    public function cleanGroupBy()
    {
        $this->_groupBy = array();
    }

    /**
     * @return array
     */
    public function getGroupBys()
    {
        return $this->_groupBy;
    }
}
