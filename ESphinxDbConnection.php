<?php

/**
 * Connection to sphinx as mysql
 */
class ESphinxDbConnection extends ESphinxBaseConnection
{
    /**
     * Target host
     *
     * @var string
     */
    public $host = 'localhost';

    /**
     * Target port
     *
     * @var int
     */
    public $port = 3306;

    /**
     * @var CDbConnection
     */
    private $db;
    private $connectionTimeout;
    private $queryTimeout;

    /**
     * Sets connection timeout
     *
     * @param int $timeout in seconds
     */
    public function setConnectionTimeout($timeout)
    {
        $this->connectionTimeout = (int)$timeout;
    }

    /**
     * Sets query timeout
     *
     * @param int $timeout in seconds
     */
    public function setQueryTimeout($timeout)
    {
        $this->queryTimeout = (int)$timeout;
    }

    /**
     * @return CDbConnection active connection to sphinx
     */
    public function getDbConnection()
    {
        if (!$this->getIsConnected()) {
            throw new ESphinxException('Connection is not opened');
        }
        return $this->db;
    }

    /**
     * Open connection to sphinx
     */
    public function openConnection()
    {
        $dsn = "mysql:host={$this->host};port={$this->port};";
        $this->db = new CDbConnection($dsn);
        $this->db->setAttribute(PDO::ATTR_TIMEOUT, $this->connectionTimeout);
        $this->db->setActive(true);
    }

    /**
     * Close connection to sphinx
     */
    public function closeConnection()
    {
        $this->getDbConnection()->setActive(false);
    }

    /**
     * Set Sphinx server connection parameters.
     *
     * @param array $parameters list of params, where first item is host, second is port
     * @example array("localhost", 3386)
     */
    public function setServer(array $parameters = array())
    {
        if(!isset ($parameters[0])) {
            $parameters[0] = $this->host;
        }
        if(!isset ($parameters[1])) {
            $parameters[1] = $this->port;
        }

        $this->host = $parameters[0];
        $this->port = $parameters[1];
    }

    /**
     * @return bool is exists active connection
     */
    public function getIsConnected()
    {
        return $this->db && $this->db->getActive();
    }

    /**
     * Prototype: function BuildExcerpts ( $docs, $index, $words, $opts=array() )
     * Excerpts (snippets) builder function. Connects to searchd, asks it to generate excerpts (snippets) from given documents, and returns the results.
     * $docs is a plain array of strings that carry the documents' contents. $index is an index name string.
     * Different settings (such as charset, morphology, wordforms) from given index will be used.
     * $words is a string that contains the keywords to highlight. They will be processed with respect to index settings.
     * For instance, if English stemming is enabled in the index, "shoes" will be highlighted even if keyword is "shoe".
     * Starting with version 0.9.9-rc1, keywords can contain wildcards, that work similarly to star-syntax available in queries.
     * $opts is a hash which contains additional optional highlighting parameters:
     * <ul>
     *   <li>"before_match": A string to insert before a keyword match. Default is "&ltb&gt".</li>
     *   <li>"after_match": A string to insert after a keyword match. Default is "&l/tb&gt".</li>
     *   <li>"chunk_separator": A string to insert between snippet chunks (passages). Default is " ... ".</li>
     *   <li>"limit": Maximum snippet size, in symbols (codepoints). Integer, default is 256.</li>
     *   <li>"around": How much words to pick around each matching keywords block. Integer, default is 5.</li>
     *   <li>"exact_phrase": Whether to highlight exact query phrase matches only instead of individual keywords. Boolean, default is false.</li>
     *   <li>"single_passage": Whether to extract single best passage only. Boolean, default is false.</li>
     *   <li>"weight_order": Whether to sort the extracted passages in order of relevance (decreasing weight), or in order of appearance in the document (increasing position). Boolean, default is false.</li>
     * </ul>
     * @param array $docs
     * @param string $index
     * @param string $words
     * @param array $opts
     * @return array
     */
    public function createExcerts(array $docs, $index, $words, array $opts =array())
    {
        $excerts = array();
        $options = "";
        $optionParams = array();
        foreach($opts as $name => $value)
        {
            $options .= ", :{$name} as {$name}";
            $optionParams[":{$name}"] = $value;
        }

        foreach($docs as $data)
        {
            $query = $this->db->createCommand("CALL SNIPPETS(:data, :index, :words {$options} )");
            $query->params = $optionParams + array(
                ":data", $data,
                ":index", $index,
                ":words", $words,
            );
            $excerts[] = $query->queryAll();
        }

        return $excerts;
    }



    /**
     * Extracts keywords from query using tokenizer settings for given index, optionally with per-keyword
     * occurrence statistics. Returns an array of hashes with per-keyword information.
     * $query is a query to extract keywords from. $index is a name of the index to get tokenizing settings and
     * keyword occurrence statistics from. $hits is a boolean flag that indicates whether keyword occurrence
     * statistics are required.
     *
     * @param string $query
     * @param string $index
     * @param boolean $hits
     * @return array
     */
    public function createKeywords($query, $index, $hits = false)
    {
        $command = $this->db->createCommand('CALL KEYWORDS(:query, :index)');
        // @todo check for hits
        $command->params = array(
            ':query'=>$query,
            ':index'=>$index,
        );
        return $command->queryAll();
    }


    /**
     * Escapes characters that are treated as special operators by the query language parser. Returns an escaped string.
     * This function might seem redundant because it's trivial to implement in any calling application.
     * However, as the set of special characters might change over time, it makes sense to have an API call that is
     * guaranteed to escape all such characters at all times.
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        $this->db->quoteValue($string);
    }


    /**
     * Execute query collection
     * @example
     * <code>
     *   $queries = array(
     *      new ESphinxQuery("hello"),
     *      new ESphinxQuery("world"),
     *   );
     *   $results = $connection->executeQueries($queries);
     *   foreach($results as $result)
     *      var_dump($result); // print ESphinxResult
     * </code>
     * @param ESphinxQuery[] $queries
     * @return ESphinxResult[]
     */
    public function executeQueries(array $queries)
    {
        $results = array();
        foreach($queries as $query) {
            $results[] = $this->executeQuery($query);
        }
        return $results;
    }


    /**
     * Execute single query.
     * @example
     * <code>
     *   $result = $connection->execute(new ESphinxQuery("hello world search"));
     *   var_dump($result); // printed ESphinxResult var dump
     * </code>
     *
     * @param ESphinxQuery $query
     * @return ESphinxResult
     * @see ESphinxQuery
     * @see ESphinxCriteria
     */
    public function executeQuery(ESphinxQuery $query)
    {
        $cb = $this->db->getCommandBuilder();
        $command = $cb->createFindCommand(
            $query->getIndexes(),
            $this->createDbCriteria($query)
        );
        $meta = $this->createMeta($command);
        return new ESphinxResult($meta);
    }


    private function createMeta(CDbCommand $command)
    {
        $matches = $command->queryAll();
        $metaInfo = $this->db->createCommand("SHOW META")->queryAll();
        $meta = array();
        foreach($metaInfo as $item)
        {
            list($name, $value) = array_values($item);
            $meta[$name] = $value;
        }
        $meta['matches'] = $matches;
        return $meta;
    }

    /**
     * @param  $query
     * @return CDbCriteria
     */
    private function createDbCriteria(ESphinxQuery $query)
    {
        $criteria = new CDbCriteria();
        $this->applySelect($criteria, $query);
        $this->applyCondition($criteria, $query);
        $this->applyGroup($criteria, $query);
        $this->applyOrder($criteria, $query);
        $this->applyLimit($criteria, $query);

        return $criteria;
    }

    /**
     * Apply limit to db criteria
     *
     * @param CDbCriteria $criteria
     * @param ESphinxQuery $query
     */
    private function applyLimit(CDbCriteria $criteria, ESphinxQuery $query)
    {
        $queryCriteria = $query->getCriteria();
        $criteria->limit = $queryCriteria->limit;
        $criteria->offset = $queryCriteria->offset;
    }

    /**
     * Apply order to db criteria
     *
     * @param CDbCriteria $criteria
     * @param ESphinxQuery $query
     */
    private function applyOrder(CDbCriteria $criteria, ESphinxQuery $query)
    {
        $queryCriteria = $query->getCriteria();
        if($queryCriteria->order)
            $criteria->order = $queryCriteria->order;
    }


    /**
     * Apply order to db criteria
     *
     * @param CDbCriteria $criteria
     * @param ESphinxQuery $query
     */
    private function applyGroup(CDbCriteria $criteria, ESphinxQuery $query)
    {
        $queryCriteria = $query->getCriteria();
        if($queryCriteria->getGroupBy()) {
            $criteria->group = $queryCriteria->getGroupBy();
        }
    }

    private function applyCondition(CDbCriteria $criteria, ESphinxQuery $query)
    {
        $queryCriteria = $query->getCriteria();

        if(strlen($query->getText()))
        {
            $criteria->addCondition('MATCH(:match)');
            $criteria->params[':match'] = $query->getText();
        }

        foreach($queryCriteria->getInConditions() as $name => $values)
            $criteria->addInCondition($name, $values);
        foreach($queryCriteria->getNotInConditions() as $name => $values)
            $criteria->addNotInCondition($name, $values);
        foreach($queryCriteria->getInRanges() as $name => $range)
            $criteria->addBetweenCondition($name, $range['min'], $range['max']);
        foreach($queryCriteria->getNotInRanges() as $name => $range)
        {
            $criteria->addCondition("{$name} NOT BETWEEN ? AND ?");
            $criteria->params[] = $range['min'];
            $criteria->params[] = $range['max'];
        }
        if($queryCriteria->getIdMax())
        {
            $criteria->addCondition('id <= :maxid');
            $criteria->params[':maxid'] = $queryCriteria->getIdMax();
        }
        if($queryCriteria->getIdMin())
        {
            $criteria->addCondition('id <= :minid');
            $criteria->params[':minid'] = $queryCriteria->getIdMin();
        }
    }

    private function applySelect(CDbCriteria $criteria, ESphinxQuery $query)
    {
        $criteria->select = $query->getCriteria()->select;
    }
}