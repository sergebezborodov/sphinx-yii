<?php


class ESphinxQlCommandBuilder extends CMysqlCommandBuilder
{
    /**
     * Creates a SELECT command for a single table.
     * @param mixed $table the table schema ({@link CDbTableSchema}) or the table name (string).
     * @param ESphinxQlCriteria $criteria the query criteria
     * @param string $alias the alias name of the primary table. Defaults to 't'.
     * @return CDbCommand query command.
     */
    public function createFindCommand($table, $criteria, $alias='t')
    {
        $sql = $this->createFindSql($table, $criteria);
        $sql .= '; SHOW META;';

        $command = $this->getDbConnection()->createCommand($sql);
        $this->bindValues($command, $criteria->params);

        return $command;
    }

    public function createMultiFindCommand(array $tables, array $criterias)
    {
        $sql = '';
        $params = array();
        $table = reset($tables);
        foreach ($criterias as $criteria) {
            $sql .= $this->createFindSql($table, $criteria);
            $sql .= '; SHOW META;';
            $params = array_merge($params, $criteria->params);

            list(,$table) = each($tables);
        }
        $command = $this->getDbConnection()->createCommand($sql);
        $this->bindValues($command, $params);

        return $command;
    }


    private function createFindSql($table, ESphinxQlCriteria $criteria)
    {
        $select = is_array($criteria->select) ? implode(', ', $criteria->select) : $criteria->select;

        $sql = ($criteria->distinct ? 'SELECT DISTINCT':'SELECT')." {$select} FROM {$table}";
        $sql = $this->applyJoin($sql, $criteria->join);
        $sql = $this->applyCondition($sql, $criteria->condition);
        $sql = $this->applyGroup($sql, $criteria->group);
        $sql = $this->applyWithinGroupOrder($sql, $criteria->withinGroupOrder);
        $sql = $this->applyHaving($sql, $criteria->having);
        $sql = $this->applyOrder($sql, $criteria->order);
        $sql = $this->applyLimit($sql, $criteria->limit, $criteria->offset);

        $sql = $this->applyOption($sql, $criteria->option);

        return $sql;
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

    protected function ensureTable(&$table)
    {}

    /**
     * Binds parameter values for an SQL command.
     * @param CDbCommand $command database command
     * @param array $values values for binding (integer-indexed array for question mark placeholders, string-indexed array for named placeholders)
     */
    public function bindValues($command, $values)
    {
        if(($n=count($values))===0)
            return;

        // by default yii checks value type and if it is float, yii sends value to pdo as string
        // mysql process this floats as string normal, but sphinx doesn't
        if(isset($values[0])) // question mark placeholders
        {
            for($i=0;$i<$n;++$i) {
                $type = is_float($values[$i]) ? PDO::PARAM_INT : null;
                $command->bindValue($i+1,$values[$i], $type);
            }
        }
        else // named placeholders
        {
            foreach($values as $name=>$value) {
                if($name[0]!==':') {
                    $name=':'.$name;
                }
                $type = is_float($value) ? PDO::PARAM_INT : null;
                $command->bindValue($name, $value, $type);
            }
        }
    }
}