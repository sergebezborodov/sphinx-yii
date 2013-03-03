<?php

/**
 * Standart connecrtion to sphinx daemon
 */
class ESphinxConnection extends EBaseSphinxConnection
{
    /**
     * Instance of SphinxClient
     * @var SphinxClient $sphinxClient
     */
    private $sphinxClient;

    /**
     * Flag check is client connected
     * @var bool defaults false
     */
    private $isConnected = false;


    public function __construct()
    {}

    /**
     * Init internal state
     */
    public function init()
    {
        $this->sphinxClient = new SphinxClient();
        $this->sphinxClient->SetArrayResult(true);
    }

    /**
     * Set Sphinx server connection parameters.
     *
     * @param array $parameters list of params, where first item is host, second is port
     * @example array("localhost", 3386)
     * @link http://www.sphinxsearch.com/docs/manual-0.9.9.html#api-func-setserver
     */
    public function setServer(array $parameters = array())
    {
        if(!isset ($parameters[0]))
            $parameters[0] = 'localhost';
        if(!isset ($parameters[1]))
            $parameters[1] = 3386;

        $this->sphinxClient->SetServer($parameters[0],$parameters[1]);
    }

    /**
     * Open Sphinx persistent connection.
     *
     * @throws ESphinxException if client is already connected.
     * @throws ESphinxException if client has connection error.
     * @link http://www.sphinxsearch.com/docs/manual-0.9.9.html#api-func-open
     */
    public function openConnection()
    {
        if ($this->isConnected) {
            throw new ESphinxException("Sphinx client is already opened");
        }

        $this->sphinxClient->Open();

        if ($this->sphinxClient->IsConnectError()) {
            throw new ESphinxException("Sphinx exception: ".$this->sphinxClient->GetLastError());
        }

        $this->isConnected = true;
    }

    /**
     * Close Sphinx persistent connection.
     *
     * @throws ESphinxException if client is not connected.
     * @link http://www.sphinxsearch.com/docs/manual-0.9.9.html#api-func-close
     */
    public function closeConnection()
    {
        if (!$this->isConnected) {
            throw new ESphinxException("Sphinx client is already closed");
        }

        $this->sphinxClient->Close();
        $this->isConnected = false;
    }

    /**
     * Check is client has connection
     *
     * @return boolean
     */
    public function getIsConnected()
    {
        return $this->isConnected;
    }

    /**
     * Sets the time allowed to spend connecting to the server before giving up.
     * Under some circumstances, the server can be delayed in responding, either due to network delays,
     * or a query backlog. In either instance, this allows the client application programmer some degree
     * of control over how their program interacts with searchd when not available, and can ensure
     * that the client application does not fail due to exceeding the script execution limits (especially in PHP).
     * In the event of a failure to connect, an appropriate error code should be returned back to the application
     * in order for application-level error handling to advise the user.
     *
     * @param integer $timeout
     * @link http://www.sphinxsearch.com/docs/manual-0.9.9.html#api-func-setconnecttimeout
     */
    public function setConnectionTimeout($timeout)
    {
        $this->sphinxClient->SetConnectTimeout((int)$timeout);
    }

    /**
     * Sets maximum search query time, in milliseconds.
     * Parameter must be a non-negative integer. Default valus is 0 which means "do not limit".
     * Similar to $cutoff setting from {@link SetLimits}, but limits elapsed query time instead of processed matches count.
     * Local search queries will be stopped once that much time has elapsed. Note that if you're performing a search
     * which queries several local indexes, this limit applies to each index separately.
     *
     * @param integer $timeout
     * @link
     */
    public function setQueryTimeout( $timeout )
    {
        $this->sphinxClient->SetMaxQueryTime((int)$timeout);
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
     *
     * @param array $docs
     * @param string $index
     * @param string $words
     * @param array $opts
     * @return array
     */
    public function createExcerts(array $docs, $index, $words, array $opts = array())
    {
        return $this->sphinxClient->BuildExcerpts($docs, $index, $words, $opts);
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
        return $this->sphinxClient->BuildKeywords($query, $index, $hits);
    }

    /**
     * Escapes characters that are treated as special operators by the query language parser. Returns an escaped string.
     * This function might seem redundant because it's trivial to implement in any calling application.
     * However, as the set of special characters might change over time, it makes sense to have an API call that is
     * guaranteed to escape all such characters at all times.
     *
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        return $this->sphinxClient->EscapeString((string)$string);
    }


    /**
     * Instantly updates given attribute values in given documents. Returns number of actually updated documents (0 or more) on success, or -1 on failure.
     *
     * @link http://sphinxsearch.com/docs/2.0.6/api-func-updateatttributes.html
     * @param $index
     * @param array $attrs
     * @param array $values
     * @param bool $mfa
     * @return int
     */
    public function update($index, array $attrs, array $values, $mfa=false)
    {
        return $this->sphinxClient->UpdateAttributes($index, $attrs, $values, $mfa);
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
        $this->resetClient();
        $this->applyQuery($query);
        $results = $this->execute();
        return $results[0];
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
        $this->resetClient();
        foreach ($queries as $query)
            $this->applyQuery($query);

        return $this->execute();
    }



    protected function applyQuery(ESphinxQuery $query)
    {
        $this->applyCriteria($query->getCriteria());
        $this->sphinxClient->AddQuery($query->getText(), $query->getIndexes());
    }

    protected function applyCriteria(ESphinxCriteria $criteria)
    {
        $this->applyMatchMode($criteria->matchMode);
        $this->applyRankMode($criteria->rankMode);
        $this->applySortMode($criteria->sortMode);
        // apply select
        if(strlen($criteria->select))
            $this->sphinxClient->SetSelect($criteria->select);
        // apply limit
        if($criteria->getIsLimited())
            $this->sphinxClient->SetLimits(
                $criteria->offset,
                $criteria->limit,
                $criteria->max_matches,
                $criteria->cutoff
            );
        // apply group
        if($criteria->getIsGroupSetted())
            $this->sphinxClient->SetGroupBy($criteria->getGroupBy(), $criteria->getGroupFunc());

        // apply id range
        if($criteria->getIsIdRangeSetted())
            $this->sphinxClient->SetIDRange(
                $criteria->getIdMin(),
                $criteria->getIdMax()
            );
        // apply weights
        $this->applyFieldWeights($criteria->getFieldWeights());
        $this->applyIndexWeights($criteria->getIndexWeights());
        // apply filters
        $this->applyFilters($criteria->getInConditions());
        $this->applyFilters($criteria->getNotInConditions(), true);
        // apply ranges
        $this->applyRanges($criteria->getInRanges());
        $this->applyRanges($criteria->getNotInRanges(),true);
    }

    protected function applyRanges(array $ranges, $exclude=false)
    {
        $exclude = (boolean)$exclude;
        foreach($ranges as $field => $range)
        {
            $isFloat = is_float($range['max']) || is_float($range['min']);
            if($isFloat)
                $this->sphinxClient->SetFilterRange(
                    $field,
                    (float)$range['min'],
                    (float)$range['max'],
                    $exclude
                );
            else
                $this->sphinxClient->SetFilterRange(
                    $field,
                    (int)$range['min'],
                    (int)$range['max'],
                    $exclude
                );
        }
    }

    protected function applyFilters(array $conditions, $exclude = false)
    {
        $exclude = (boolean)$exclude;
        foreach	($conditions as $field => $values)
        {
            $this->sphinxClient->SetFilter($field, $values, $exclude);
        }
    }

    protected function applyIndexWeights(array $weights)
    {
        foreach( $weights as $index => $weight )
            $weights[$index] = (int)$weight;

        $this->sphinxClient->SetIndexWeights($weights);
    }

    protected function applyFieldWeights(array $weights)
    {
        foreach( $weights as $field => $weight )
            $weights[$field] = (int)$weight;

        $this->sphinxClient->SetFieldWeights($weights);
    }

    protected function applySortMode($mode)
    {
        $mode = (int)$mode;
        if(in_array($mode,ESphinxCriteria::$sortModes))
            $this->sphinxClient->SetSortMode($mode);
        else
            throw new ESphinxException("Search mode {$mode} is undefined");
    }

    protected function applyMatchMode($mode)
    {
        $mode = (int)$mode;
        if(in_array($mode, ESphinxCriteria::$matchModes))
            $this->sphinxClient->SetMatchMode($mode);
        else
            throw new ESphinxException("Match mode {$mode} is not defined");
    }

    protected function applyRankMode($mode)
    {
        $mode = (int)$mode;
        if(in_array($mode, ESphinxCriteria::$rankModes))
            $this->sphinxClient->SetRankingMode($mode);
    }

    /**
     * Reset internal state of sphinxClient
     */
    protected function resetClient()
    {
        $this->sphinxClient->ResetFilters();
        $this->sphinxClient->ResetGroupBy();
        $this->sphinxClient->ResetOverrides();
        $this->sphinxClient->SetLimits(0, 20);
        $this->sphinxClient->SetArrayResult(true);
        $this->sphinxClient->SetFieldWeights(array());
        $this->sphinxClient->SetIDRange(0,0);
        $this->sphinxClient->SetIndexWeights(array());
        $this->sphinxClient->SetMatchMode(SPH_MATCH_EXTENDED2);
        $this->sphinxClient->SetRankingMode(SPH_RANK_NONE);
        $this->sphinxClient->SetSortMode(SPH_SORT_RELEVANCE, "");
        $this->sphinxClient->SetSelect("*");
    }

    protected function execute()
    {
        $sph = $this->sphinxClient->RunQueries();

        if( $error = $this->sphinxClient->GetLastError() )
            throw new ESphinxException($error);
        if( $error = $this->sphinxClient->GetLastWarning() )
            throw new ESphinxException($error);
        if( !is_array($sph) )
            throw new ESphinxException("Sphinx client returns result not array");

        $results = array();
        foreach($sph as $result)
        {
            if(isset($result['error']) && strlen($result['error']))
                throw new ESphinxException($result['error']);
            $results[] = new ESphinxResult($result);
        }
        return $results;
    }
}
