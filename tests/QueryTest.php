<?php


class QueryTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $query = new ESphinxQuery('text', 'index');
        $this->assertEquals($query->getText(), 'text');
        $this->assertEquals($query->getIndexes(), 'index');
    }
}
