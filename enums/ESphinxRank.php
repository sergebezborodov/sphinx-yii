<?php

/**
 * Sphinx rank mode types
 *
 * @see http://sphinxsearch.com/docs/current.html#weighting
 */
class ESphinxRank
{
    /**
     * Default ranking mode which uses and combines both phrase proximity and BM25 ranking.
     *
     * @const int 0
     */
    const PROXIMITY_BM25 = 0;

    /**
     * Statistical ranking mode which uses BM25 ranking only (similar to most other full-text engines).
     * This mode is faster but may result in worse quality on queries which contain more than 1 keyword.
     *
     * @const int 1
     */
    const BM25 = 1;

    /**
     * Disabled ranking mode. This mode is the fastest.
     * It is essentially equivalent to boolean searching.
     * A weight of 1 is assigned to all matches.
     *
     * @const int 2
     */
    const NONE = 2;

    /**
     * Ranking by keyword occurrences count. This ranker computes the amount of per-field keyword occurrences,
     * then multiplies the amounts by field weights, then sums the resulting values for the final result.
     *
     * @const int 3
     */
    const WORDCOUNT = 3;

    /**
     * Added in version 0.9.9-rc1, returns raw phrase proximity value as a result.
     * This mode is internally used to emulate SPH_MATCH_ALL queries.
     *
     * @const int 4
     */
    const PROXIMITY = 4;

    /**
     * Added in version 0.9.9-rc1, returns rank as it was computed in SPH_MATCH_ANY mode earlier,
     * and is internally used to emulate SPH_MATCH_ANY queries
     *
     * @const int 5
     */
    const MATCHANY  = 5;

    /**
     * Added in version 0.9.9-rc2, returns a 32-bit mask with N-th bit corresponding to N-th fulltext field,
     * numbering from 0. The bit will only be set when the respective field has any keyword
     * occurences satisfiying the query
     *
     * @const int 6
     */
    const FIELDMASK = 6;

    /**
     * SPH_RANK_SPH04, added in version 1.10-beta, is generally based on the default SPH_RANK_PROXIMITY_BM25 ranker,
     * but additionally boosts the matches when they occur in the very beginning or the very end of a text field.
     * Thus, if a field equals the exact query, SPH04 should rank it higher than a field that contains
     * the exact query but is not equal to it. (For instance, when the query is "Hyde Park",
     * a document entitled "Hyde Park" should be ranked higher than
     * a one entitled "Hyde Park, London" or "The Hyde Park Cafe".)
     */
    const SPH04 = 7;

    const EXPR = 8;

    const TOTAL = 9;


    /**
     * List all rank modes.
     *
     * @return array
     */
    public static function items()
    {
        return array(
            self::PROXIMITY_BM25 => 'proximity_bm25',
            self::BM25           => 'bm25',
            self::NONE           => 'none',
            self::WORDCOUNT      => 'wordcount',
            self::PROXIMITY      => 'proximity',
            self::MATCHANY       => 'matchany',
            self::FIELDMASK      => 'fieldmask',
            self::SPH04          => 'sph04',
            self::EXPR           => 'expr',
        );
    }

    public static function item($key)
    {
        $items = self::items();
        return $key ? $items[$key] : null;
    }

    /**
     * Is valid value for struct
     *
     * @param int $value
     * @return bool
     */
    public static function isValid($value)
    {
        return array_key_exists($value, self::items());
    }
}
