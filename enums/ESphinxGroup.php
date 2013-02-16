<?php

/**
 * Sphinx group mode types
 */
class ESphinxGroup
{
    /**
     * Extracts year, month and day in YYYYMMDD format from timestamp
     *
     * @const int 0
     */
    const BY_DAY = 0;

    /**
     * Extracts year and first day of the week number (counting from year start) in YYYYNNN format from timestamp.
     *
     * @const int 1
     */
    const BY_WEEK = 1;

    /**
     * Extracts month in YYYYMM format from timestamp.
     *
     * @const int 2
     */
    const BY_MONTH = 2;

    /**
     * Extracts year in YYYY format from timestamp.
     *
     * @const int 3
     */
    const BY_YEAR = 3;

    /**
     * Uses attribute value itself for grouping
     *
     * @const int 4
     */
    const BY_ATTR = 4;

    /**
     * List all group modes.
     *
     * @see http://sphinxsearch.com/docs/manual-0.9.9.html#clustering
     * @return array
     */
    public static function items()
    {
        return array(
            self::BY_DAY,
            self::BY_WEEK,
            self::BY_MONTH,
            self::BY_YEAR,
            self::BY_ATTR,
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
