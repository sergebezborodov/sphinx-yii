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
}