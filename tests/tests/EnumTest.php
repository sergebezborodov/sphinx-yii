<?php

/**
 * Tests for enums
 */
class EnumTest extends PHPUnit_Framework_TestCase
{
    public function testGroup()
    {
        $this->assertEquals(ESphinxGroup::BY_DAY, SPH_GROUPBY_DAY);
        $this->assertEquals(ESphinxGroup::BY_WEEK, SPH_GROUPBY_WEEK);
        $this->assertEquals(ESphinxGroup::BY_MONTH, SPH_GROUPBY_MONTH);
        $this->assertEquals(ESphinxGroup::BY_YEAR, SPH_GROUPBY_YEAR);
        $this->assertEquals(ESphinxGroup::BY_ATTR, SPH_GROUPBY_ATTR);

        $this->assertCount(5, ESphinxGroup::items());
    }

    public function testMath()
    {
        $this->assertEquals(ESphinxMatch::ALL, SPH_MATCH_ALL);
        $this->assertEquals(ESphinxMatch::ANY, SPH_MATCH_ANY);
        $this->assertEquals(ESphinxMatch::PHRASE, SPH_MATCH_PHRASE);
        $this->assertEquals(ESphinxMatch::BOOLEAN, SPH_MATCH_BOOLEAN);
        $this->assertEquals(ESphinxMatch::EXTENDED, SPH_MATCH_EXTENDED);
        $this->assertEquals(ESphinxMatch::FULLSCAN, SPH_MATCH_FULLSCAN);
        $this->assertEquals(ESphinxMatch::EXTENDED2, SPH_MATCH_EXTENDED2);

        $this->assertCount(7, ESphinxMatch::items());
    }

    public function testRank()
    {
        $this->assertEquals(ESphinxRank::PROXIMITY_BM25, SPH_RANK_PROXIMITY_BM25);
        $this->assertEquals(ESphinxRank::BM25, SPH_RANK_BM25);
        $this->assertEquals(ESphinxRank::NONE, SPH_RANK_NONE);
        $this->assertEquals(ESphinxRank::WORDCOUNT, SPH_RANK_WORDCOUNT);
        $this->assertEquals(ESphinxRank::PROXIMITY, SPH_RANK_PROXIMITY);
        $this->assertEquals(ESphinxRank::MATCHANY, SPH_RANK_MATCHANY);
        $this->assertEquals(ESphinxRank::FIELDMASK, SPH_RANK_FIELDMASK);
        $this->assertEquals(ESphinxRank::SPH04, SPH_RANK_SPH04);
        $this->assertEquals(ESphinxRank::EXPR, SPH_RANK_EXPR);
        $this->assertEquals(ESphinxRank::TOTAL, SPH_RANK_TOTAL);

        $this->assertCount(9, ESphinxRank::items());
    }

    public function testSort()
    {
        $this->assertEquals(ESphinxSort::RELEVANCE, SPH_SORT_RELEVANCE);
        $this->assertEquals(ESphinxSort::ATTR_DESC, SPH_SORT_ATTR_DESC);
        $this->assertEquals(ESphinxSort::ATTR_ASC, SPH_SORT_ATTR_ASC);
        $this->assertEquals(ESphinxSort::TIME_SEGMENTS, SPH_SORT_TIME_SEGMENTS);
        $this->assertEquals(ESphinxSort::EXTENDED, SPH_SORT_EXTENDED);
        $this->assertEquals(ESphinxSort::EXPR, SPH_SORT_EXPR);

        $this->assertCount(6, ESphinxSort::items());
    }
}
