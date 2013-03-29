<?php

/**
 * Sphinx Math Modes
 */
class ESphinxMatch
{
    /**
     * Matches all query words (default mode).
     *
     * @const int 0
     */
    const ALL = 0;

    /**
     * Matches any of the query words.
     *
     * @const int 1
     */
    const ANY = 1;

    /**
     * matches query as a phrase, requiring perfect match.
     *
     * @const int 2
     */
    const PHRASE = 2;

    /**
     * Matches query as a boolean expression.
     *
     * @see http://sphinxsearch.com/docs/manual-0.9.9.html#boolean-syntax
     * @const int 3
     */
    const BOOLEAN = 3;

    /**
     * Matches query as an expression in Sphinx internal query language.
     * As of 0.9.9, this has been superceded by SPH_MATCH_EXTENDED2, providing additional functionality
     * and better performance. The ident is retained for legacy application code that will continue to be
     * compatible once Sphinx and its components, including the API, are upgraded.
     *
     * @see http://sphinxsearch.com/docs/manual-0.9.9.html#extended-syntax "Section 4.3, Extended query syntax"
     * @const int 4
     */
    const EXTENDED = 4;

    /**
     * Matches query, forcibly using the "full scan" mode as below. NB, any query terms will be ignored, such that
     * filters, filter-ranges and grouping will still be applied, but no text-matching.
     *
     * @const int 5
     */
    const FULLSCAN = 5;

    /**
     * Matches query using the second version of the Extended matching mode.
     *
     * @const int 6
     */
    const EXTENDED2 = 6;

    /**
     * All match mode variants.
     *
     * @see http://sphinxsearch.com/docs/manual-0.9.9.html#matching-modes
     * @return array
     */
    public static function items()
    {
        return array(
            self::ALL,
            self::ANY,
            self::PHRASE,
            self::BOOLEAN,
            self::EXTENDED,
            self::FULLSCAN,
            self::EXTENDED2,
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
