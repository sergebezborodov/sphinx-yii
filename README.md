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

```php
'components' => array(
    'sphinx' => array(
        'class' => 'ext.sphinx.ESphinxConnection',
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
```php
Yii::app()->sphinx->executeQuery(new ESphinxQuery('Hello world!'));
```

Query in index:
```php
Yii::app()->sphinx->executeQuery(new ESphinxQuery('Hello world!'), 'blog');
```


Extended queries
----------------
Often we need search in index with some parametrs and options. For this task component has class ESphinxSearchCriteria.
It's very similar to CDbCriteria and has the same idea.

Search in article index with some parametrs:

```php
$criteria = new ESphinxSearchCriteria(array(
    'sortMode' => ESphinxSort::EXTENDED,
    'orders' => array(
        'date_created' => 'DESC',
        'date_updated' => 'ASC',
    ),
    'mathMode' => ESphinxMatch::EXTENDED,
));

$query = new ESphinxQuery('@(title,body) hello world', 'articles', $criteria);
```

Criteria can changing in work.
```php
$criteria = new ESphinxSearchCriteria(array('mathMode' => ESphinxMatch::EXTENDED));
$criteria->addFilter('user_id', 1000); // add filter by user, we can use integer or integer array
$criteria->addFilter('site_id', 123, false, 'site'); // add filter by site_id field with key value (will used later)

// querying
$result = Yii::app()->sphinx->executeQuery(new ESphinxQuery('', 'products', $criteria));

// search same query by another site
$criteria->addFilter('site_id', 321, false, 'site'); // change site_id param value

// querying
$result = Yii::app()->sphinx->executeQuery(new ESphinxQuery('', 'products', $criteria));

// search same query but without site_id param
$criteria->deleteFilter('site'); // delete filter on site_id field

// querying....
```
