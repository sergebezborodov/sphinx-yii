<?php


class ESphinxDbCommandBuilder extends CMysqlCommandBuilder
{
    /**
     * Creates a SELECT command for a single table.
     * @param mixed $table the table schema ({@link CDbTableSchema}) or the table name (string).
     * @param ESphinxDbCriteria $criteria the query criteria
     * @param string $alias the alias name of the primary table. Defaults to 't'.
     * @return CDbCommand query command.
     */
    public function createFindCommand($table,$criteria,$alias='t')
    {
        $this->ensureTable($table);
        $select = is_array($criteria->select) ? implode(', ', $criteria->select) : $criteria->select;
        if ($criteria->alias != '') {
            $alias = $criteria->alias;
        }
        $alias = $this->getSchema()->quoteTableName($alias);

        // issue 1432: need to expand * when SQL has JOIN
        if ($select === '*' && !empty($criteria->join))
        {
            $prefix = $alias . '.';
            $select = array();
            foreach($table->getColumnNames() as $name) {
                $select[] = $prefix.$this->_schema->quoteColumnName($name);
            }
            $select = implode(', ',$select);
        }

        $sql = ($criteria->distinct ? 'SELECT DISTINCT':'SELECT')." {$select} FROM {$table->rawName} $alias";
        $sql = $this->applyJoin($sql, $criteria->join);
        $sql = $this->applyCondition($sql, $criteria->condition);
        $sql = $this->applyGroup($sql, $criteria->group);
        $sql = $this->applyHaving($sql, $criteria->having);
        $sql = $this->applyOrder($sql, $criteria->order);
        $sql = $this->applyWithinGroupOrder($sql, $criteria->withinGroupOrder);
        $sql = $this->applyLimit($sql, $criteria->limit, $criteria->offset);

        $sql = $this->applyOption($sql, $criteria->option);

        $command=$this->getDbConnection()->createCommand($sql);
        $this->bindValues($command, $criteria->params);
        return $command;
    }


    public function applyOption($sql, $option)
    {
        if ($option) {
            $sql .= ' OPTION ' . $option;
        }
        return $sql;
    }

    public function applyWithinGroupOrder($sql, $groupOrder)
    {
        if ($groupOrder) {
            $sql .= ' WITHIN GROUP ORDER BY ' . $groupOrder;
        }
        return $sql;
    }
}