<?php


class SearchTest extends CDbTestCase
{
    public function setUp()
    {
        $this->getFixtureManager()->resetTable('article');
        $this->getFixtureManager()->loadFixture('article');

        exec('./setup.sh');

        parent::setUp();
    }


    /**
     * @return ESphinxConnection
     */
    protected function createConnection()
    {
        $sphinx = new ESphinxConnection;
        $sphinx->setServer(array('localhost', 9876));
        $sphinx->init();

        return $sphinx;
    }

    public function testCreate()
    {
        $sphinx = $this->createConnection();
        $this->assertInstanceOf('ESphinxConnection', $sphinx);
        $this->assertFalse($sphinx->getIsConnected());
    }

    public function testSimpleQuery()
    {
        $sphinx = $this->createConnection();

        $query = new ESphinxQuery('First Article with Title');

        $result = $sphinx->executeQuery($query);
        $this->assertInstanceOf('ESphinxResult', $result);
        $this->assertEquals($result->getFound(), 1);

        /** @var ESphinxMatchResult $math */
        $math = $result[0];

        $this->assertEquals($math->getId(), 1);
        $this->assertEquals($math->getAttribute('id'), 1);
    }

    public function testQueryWithParams()
    {
        $sphinx = $this->createConnection();

        $query = new ESphinxQuery('Article with Title', '*', array(
            'filters' => array(
                array('user_id', array(1000, 2000))
            )
        ));
        $result = $sphinx->executeQuery($query);
        $this->assertInstanceOf('ESphinxResult', $result);

        $this->assertEquals($result->getFound(), 2);
    }
}