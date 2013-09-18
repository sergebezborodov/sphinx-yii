<?php

require_once 'ApiSearchTest.php';

/**
 * Test for search with mysql connection interface
 */
class DbSearchTest extends ApiSearchTest
{
    public function setUp()
    {}

    /**
     * @return ESphinxMysqlConnection
     */
    protected function createConnection()
    {
        $sphinx = new ESphinxMysqlConnection;
        $sphinx->setServer(array('127.0.0.1', 9888));
        $sphinx->init();

        return $sphinx;
    }

    public function testCreate()
    {
        $sphinx = $this->createConnection();
        $this->assertInstanceOf('ESphinxMysqlConnection', $sphinx);
        $this->assertTrue($sphinx->getIsConnected());
    }
}
