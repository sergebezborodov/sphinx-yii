<?php


class QueryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $query = new ESphinxQuery('text', 'index');
        $this->assertEquals($query->getText(), 'text');
        $this->assertEquals($query->getIndexes(), 'index');
    }

    public function testWithCriteria()
    {
        $criteriaData = array(
            'rankingMode' => ESphinxRank::BM25,
            'sortMode'    => ESphinxSort::ATTR_DESC,
            'sortBy'      => 'field1',
        );
        $criteria = new ESphinxSearchCriteria($criteriaData);
        $query = new ESphinxQuery('text', 'index', $criteria);
        $this->assertEquals($criteria, $query->getCriteria());
    }
}
