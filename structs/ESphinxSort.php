<?php

/**
 * Sphinx sort mode types
 */
class ESphinxSort
{
    /**
     * Sorts by relevance in descending order (best matches first).
     *
     * @const int 0
     */
    const RELEVANCE = 0;

    /**
     * Sorts by an attribute in descending order (bigger attribute values first).
     *
     * @const int 1
     */
    const ATTR_DESC = 1;

    /**
     * Sorts by an attribute in ascending order (smaller attribute values first).
     *
     * @const int 2
     */
    const ATTR_ASC = 2;

    /**
     * Sorts by time segments (last hour/day/week/month) in descending order,
     * and then by relevance in descending order.
     *
     * @const int 3
     */
    const TIME_SEGMENTS = 3;

    /**
     * Sorts by SQL-like combination of columns in ASC/DESC order.
     *
     * @const int 4
     */
    const EXTENDED = 4;

    /**
     * Sorts by an arithmetic expression.
     *
     * @const int 5
     */
    const EXPR = 5;

    /**
     * List all sort modes.
     *
     * @see http://sphinxsearch.com/docs/manual-0.9.9.html#sorting-modes
     * @return array
     */
    public static function items()
    {
        return array(
            self::RELEVANCE,
            self::ATTR_DESC,
            self::ATTR_ASC,
            self::TIME_SEGMENTS,
            self::EXTENDED,
            self::EXPR,
        );
    }

    /**
     * Is valid value for struct
     *
     * @param int $value
     * @return bool
     */
    public static function isValid($value)
    {
        return in_array($value, self::items());
    }
}
