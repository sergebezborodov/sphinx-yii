<?php


Yii::app()->db->createCommand('DROP TABLE IF EXISTS article')->execute();

Yii::app()->db->createCommand()->createTable('article', array(
    'id'           => 'pk',
    'title'        => 'string',
    'content'      => 'TEXT',
    'user_id'      => 'int',
    'rating'       => 'float',
    'date_updated' => 'timestamp',
));
