<?php

/**
 * Class represents query results from sphinx
 */
class ESphinxResult extends CComponent implements Iterator
{
	/**
	 * @var array
	 */
	private $_result;

	/**
	 * @var array
	 */
	private $_matches;

	/**
	 * @var array
	 */
	private $_row;

	/**
	 * @var int
	 */
	private $_index = -1;

	/**
	 * @var int
	 */
	private $_found;

	/**
	 * @var int
	 */
	private $_found_total;

	/**
	 * @var array
	 */
	private $_match_objects;

	/**
	 * @param array $result
	 */
	public function __construct(array $result)
	{
		$this->_result = $result;
	    $this->_matches = isset($result['matches']) ? $result['matches'] : array();
	}

	/**
	 * @return int
	 */
	public function getFound()
	{
		if ($this->_found === null) {
			$this->_found = (int)$this->_result['total'];
		}
	    return $this->_found;
	}

	/**
	 * @return int
	 */
	public function getFoundTotal()
	{
		if ($this->_found_total === null) {
			$this->_found_total = (int)$this->_result['total_found'];
		}
	    return $this->_found_total;
	}

    /**
     * @param $attribute
     * @return array
     */
    public function getAttributeList($attribute)
	{
		$list = array();
		foreach($this as $match) {
			$list[] = $match->getAttribute($attribute);
        }

		return $list;
	}

	/**
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current()
	{
		return $this->_row;
	}

	/**
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, integer
	 * 0 on failure.
	 */
	public function key()
	{
		return $this->_index;
	}

	/**
	 * Move forward to next element
     *
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next()
	{
		$this->_index++;
		if(isset($this->_matches[$this->_index])) {
			if(!isset($this->_match_objects[$this->_index])) {
				$this->_match_objects[$this->_index] = new ESphinxMatch($this->_matches[$this->_index]);
			}
	        $this->_row = $this->_match_objects[$this->_index];
		} else {
		    $this->_row = null;
        }
	}

	/**
	 * Rewind the Iterator to the first element
     *
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind()
	{
		$this->_index = -1;
	    $this->next();
	}

    /**
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid()
	{
		return $this->_row !== null;
	}
}


class ESphinxMatch extends CComponent
{
	private $_match;
	private $_weight;
	private $_id;
	private $_attributes;

    /**
     * @param array $match
     */
    public function __construct(array $match)
	{
		$this->_match = $match;
	    $this->_id = (int)$match['id'];
	    $this->_weight = (int)$match['weight'];
	    $this->_attributes = (array)$match['attrs'];
	}

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
	{
		if(isset($this->_attributes[$name])) {
			return $this->_attributes[$name];
        } else if(isset($this->_match[$name])) {
			return $this->_match[$name];
        }

        return parent::__get($name);
	}

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
	{
		return isset($this->_match[$name])
			|| isset($this->_attributes[$name])
			|| parent::__isset($name);
	}

    /**
     * @param string $name
     * @return mixed
     * @throws ESphinxException
     */
    public function getAttribute($name)
	{
		if(isset($this->_attributes[$name])) {
			return $this->_attributes[$name];
        } else if(isset($this->_match[$name])) {
			return $this->_match[$name];
        }

        throw new ESphinxException("Attribute \"{$name}\" is not defined");
	}

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute($name)
	{
		return isset($this->_match[$name]) || isset($this->_attributes[$name]);
	}
}