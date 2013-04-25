<?php
/**
 * Sphinx Data Provider
 *
 * Runs a Sphinx search based on a specified query, selects the model instances
 * from the database and returns an ActiveDataProvider
 *
 *
 * SphinxDataProvider may be used in the following way:
 * <pre>
 * $dataProvider=new SphinxDataProvider('Post', array(
 *     'query'=>'@name mark',
 *     'sphinxCriteria' => new ESphinxCriteria(array(
 *        'matchMode' => ESphinxMatch::EXTENDED,
 *      )),
 *     'criteria'=>array(
 *         'condition'=>'status=1',
 *         'order'=>'create_time DESC',
 *         'with'=>array('author'),
 *     ),
 *     'pagination'=>array(
 *         'pageSize'=>20,
 *     ),
 * ));
 * // $dataProvider->getData() will return a list of Post objects
 * </pre>
 *
 */
class SphinxDataProvider extends CActiveDataProvider
{

  public $query;
  public $sphinxCriteria;
  public $limit = 100;

  private $_result;

  /**
  * Constructor.
  * @param mixed $modelClass the model class (e.g. 'Post') or the model finder instance
  * (e.g. <code>Post::model()</code>, <code>Post::model()->published()</code>).
  * @param array $config configuration (name=>value) to be applied as the initial property values of this class.
  */
  public function __construct($modelClass,$config=array())
  {
    parent::__construct($modelClass,$config);

    // No ESphinxCriteria object in config?
    if (!$this->sphinxCriteria) {
      // Set a new sphinx criteria
      $this->sphinxCriteria = new ESphinxSearchCriteria(array(
        'matchMode' => ESphinxMatch::EXTENDED,
      ));
    }
    // Set the limit
    $this->sphinxCriteria->limit = $this->limit;
  }

  /**
   * Fetches the data from the persistent data storage.
   * @return array list of data items
   */
  protected function fetchData()
  {

    $criteria=clone $this->getCriteria();

    // Execute the search query and get the result
    if (!$result = $this->getResult()) {
      return array();
    }

    if (($pagination=$this->getPagination())!==false) {
      $pagination->setItemCount($this->getTotalItemCount());
      $pagination->applyLimit($criteria);
      // Are we paginating beyond the initial limit of the search?
      if ($criteria->offset + $criteria->limit > $this->sphinxCriteria->limit) {
        // Set the search offset
        $this->sphinxCriteria->offset = $criteria->offset;
        // Reset the ActiveRecord offset
        $criteria->offset = 0;
        // Re-query
        $result = $this->getResult(true);
      }

    }

    // Get an array of model IDs from the search result
    $ids = $result->getAttributeList('id');

	// No results in search? Return empty array
    if (!count($ids)) {
      return $ids;
    }

	// Convert array of IDs to CSV
    $ids_list = implode(',',$ids);

    // Set the query condition to find models for the search results
    $condition = 't.id IN('.$ids_list.')';
    if ($criteria->condition) {
      $criteria->condition .= ' AND '.$condition;
    } else {
      $criteria->condition = $condition;
    }

    // Set the order to match the order the IDs are returned from Sphinx
    $order = 'FIELD(t.id, '.$ids_list.')';
    if ($criteria->order) {
      $criteria->order .= ','.$order;
    } else {
      $criteria->order = $order;
    }

    // Set the sorting criteria on the limited search results
    $baseCriteria=$this->model->getDbCriteria(false);
    if (($sort=$this->getSort())!==false) {
      // set model criteria so that CSort can use its table alias setting
      if($baseCriteria!==null) {
        $c=clone $baseCriteria;
        $c->mergeWith($criteria);
        $this->model->setDbCriteria($c);
      } else {
        $this->model->setDbCriteria($criteria);
      }
      $sort->applyOrder($criteria);
    }
    $this->model->setDbCriteria($baseCriteria!==null ? clone $baseCriteria : null);
    // Select the models
    $data=$this->model->findAll($criteria);
    $this->model->setDbCriteria($baseCriteria);  // restore original criteria
    return $data;
  }

  /**
   * Calculates the total number of data items.
   * @return integer the total number of data items.
   */
  protected function calculateTotalItemCount()
  {
    if (!$result = $this->getResult()) {
      return 0;
    }
    return $result->getFoundTotal();
  }

  /**
   * Get the results of the Sphinx search query
   *
   * @param bool $refresh Do we want to force the query to re-run
   * @return ESphinxResult|false The search results
   */
  protected function getResult($refresh=false)
  {
    if ($this->_result === null || $refresh) {
      // Execute the search query
      $this->_result = Yii::app()->sphinx->executeQuery(
        new ESphinxQuery(trim($this->query), '*', $this->sphinxCriteria)
      );
    }
    // Has the search found any results?
    if ($this->_result->getFoundTotal() == 0) {
      return false;
    }
    return $this->_result;
  }
}
