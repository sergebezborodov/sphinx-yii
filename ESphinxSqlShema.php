<?php


class ESphinxSqlShema extends CMysqlSchema
{
    /**
     * Creates a command builder for the database.
     * This method may be overridden by child classes to create a DBMS-specific command builder.
     * @return CDbCommandBuilder command builder instance
     */
    protected function createCommandBuilder()
    {
        return new ESphinxDbCommandBuilder($this);
    }
}