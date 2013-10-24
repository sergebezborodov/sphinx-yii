<?php

/**
 * Class represents query results from sphinx
 */
class ESphinxResult extends CComponent implements Iterator, ArrayAccess, Countable
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
        unset($this->_result['matches']);
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
		if (isset($this->_matches[$this->_index])) {
			if(!isset($this->_match_objects[$this->_index])) {
				$this->_match_objects[$this->_index] = new ESphinxMatchResult($this->_matches[$this->_index]);
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

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->_matches[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        if (!isset($this->_matches[$offset])) {
            return null;
        }

        if (!isset($this->_match_objects[$offset])) {
            $this->_match_objects[$offset] = new ESphinxMatchResult($this->_matches[$offset]);
        }
        return $this->_match_objects[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        throw new ESphinxException('Search result is readonly');
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new ESphinxException('Search result is readonly');
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->_matches);
    }
}


class ESphinxMatchResult extends CComponent
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
	    $this->_attributes = isset($match['attrs']) ? (array)$match['attrs'] : $match;
	}

    public function getId()
    {
        return $this->_id;
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

    public function getAttributes()
    {
        return $this->_attributes;
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