<?php

/**
 * Class implements sphinx query model.
 * Query contains search text, indexes list, and sphinx criteria.
 *
 * @property ESphinxSearchCriteria $criteria
 * @property string $text
 * @property string $indexes
 */
class ESphinxQuery extends CComponent
{
	/**
	 * @var string
	 */
	private $_text;

	/**
	 * @var string
	 */
	private $_indexes;

	/**
	 * @var ESphinxSearchCriteria
	 */
	private $_criteria;

	/**
	 * Query constructor.
	 * 
	 * @param string $text search phrase
	 * @param string $indexes list of indexes
	 * @param ESphinxSearchCriteria|array $criteria search criteria
	 */
    public function __construct($text, $indexes = "*", $criteria = null)
	{
		$this->_text = (string)$text;

		if ($criteria instanceof ESphinxSearchCriteria) {
	        $this->_criteria = $criteria;
        } else if(is_array($criteria)) {
			$this->_criteria = new ESphinxSearchCriteria($criteria);
        } else {
            $this->_criteria = new ESphinxSearchCriteria;
        }

	    if (is_array($indexes)) {
            $this->_indexes = implode(" ", $indexes);
        } else {
            $this->_indexes = (string)$indexes;
        }
	}

	/**
	 * Get search query
     *
	 * @return string
	 */
	public function getText()
	{
		return $this->_text;
	}

	/**
	 * Get list indexes as string
     *
	 * @return string
	 */
	public function getIndexes()
	{
		return $this->_indexes;
	}

	/**
	 * Get search criteria
     *
	 * @return ESphinxSearchCriteria
	 */
	public function getCriteria()
	{
		return $this->_criteria;
	}
}
