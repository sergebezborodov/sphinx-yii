Yii Sphinx Component
====================

Simple and powerful component for work with Sphinx search engine.

Features
--------

* Simple query methods
* Extended ESphinxSearchCriteria for complex queries
* Support packeted queries
* Unit tests coverage


Configure
----------

```
'components' => array(
    'sphinx' => array(
        'server' => array('localhost', 3386),
        'connectionTimeout' => 3, // optional, default 0 - no limit
        'queryTimeout'      => 5, // optional, default 0 - no limit
    ),
),
```


How to use
----------

All component classes names begins with ESphinx.
Main object we used for querying is ESphinxQuery.

Simple query with text in all indexes:
```
Yii::app()->sphinx->executeQuery(new ESphinxQuery('Hello world!'));
```