<?php


/**
 * DB connection to sphinx as mysql
 */
class ESphinxQlDbConnection extends CDbConnection
{
    /**
     * @var ESphinxQlCommandBuilder
     */
    private $_builder;

    /**
     * Returns the SQL command builder for the current DB connection.
     * @return ESphinxQlCommandBuilder the command builder
     */
    public function getCommandBuilder()
    {
        if ($this->_builder === null) {
            $this->_builder = new ESphinxQlCommandBuilder($this->getSchema());
        }
        return $this->_builder;
    }
}