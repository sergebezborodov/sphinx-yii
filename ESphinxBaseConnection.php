<?php

/**
 * Base Sphinx Connection class
 */
abstract class ESphinxBaseConnection extends CComponent
{
    const DEFAULT_SERVER = '127.0.0.1';
    const DEFAULT_PORT   = 3314;

    /**
     * Init internal state
     */
    public function init()
    {}


    /**
     * Set Sphinx server connection parameters.
     *
     * @param array $parameters list of params, where first item is host, second is port
     * @example 'localhost'
     * @example 'localhost:3314'
     * @example array("localhost", 3386)
     * @link http://sphinxsearch.com/docs/current.html#api-func-setserver
     */
    abstract public function setServer($parameters = null);

    /**
     * Open Sphinx persistent connection.
     * @throws ESphinxException if client is already connected.
     * @throws ESphinxException if client has connection error.
     * @link http://sphinxsearch.com/docs/current.html#api-func-open
     */
    abstract public function openConnection();

    /**
     * Close Sphinx persistent connection.
     * @throws ESphinxException if client is not connected.
     * @link http://sphinxsearch.com/docs/current.html#api-func-close
     */
    abstract public function closeConnection();

    /**
     * Check is client has connection
     * @return boolean
     */
    abstract public function getIsConnected();

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
     * @link http://sphinxsearch.com/docs/current.html#api-func-setconnecttimeout
     */
    abstract function setConnectionTimeout($timeout);

    /**
     * Sets maximum search query time, in milliseconds.
     * Parameter must be a non-negative integer. Default valus is 0 which means "do not limit".
     * Similar to $cutoff setting from {@link SetLimits}, but limits elapsed query time instead of processed matches count.
     * Local search queries will be stopped once that much time has elapsed. Note that if you're performing a search
     * which queries several local indexes, this limit applies to each index separately.
     * @param integer $timeout
     * @link http://sphinxsearch.com/docs/current.html#api-func-setmaxquerytime
     */
    abstract public function setQueryTimeout( $timeout );

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
     * @link http://sphinxsearch.com/docs/current.html#api-func-buildexcerpts
     */
    abstract public function createExcerts(array $docs, $index, $words, array $opts = array());

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
     * @link http://sphinxsearch.com/docs/current.html#api-func-buildkeywords
     */
    abstract public function createKeywords($query, $index, $hits = false);

    /**
     * Escapes characters that are treated as special operators by the query language parser. Returns an escaped string.
     * This function might seem redundant because it's trivial to implement in any calling application.
     * However, as the set of special characters might change over time, it makes sense to have an API call that is
     * guaranteed to escape all such characters at all times.
     * @param string $string
     * @return string
     * @link http://sphinxsearch.com/docs/current.html#api-func-escapestring
     */
    abstract public function escape($string);

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
    abstract public function executeQuery(ESphinxQuery $query);

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
    abstract public function executeQueries(array $queries);
}
