<?php

/**
 * Sphinx rank mode types
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
     * List all rank modes.
     *
     * @return array
     */
    public static function items()
    {
        return array(
            self::PROXIMITY_BM25,
            self::BM25,
            self::NONE,
            self::WORDCOUNT,
            self::MATCHANY,
            self::FIELDMASK,
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
