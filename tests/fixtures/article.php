<?php


return array(
    array(
        'id' => 1,
        'title' => 'First Article with Title',
        'content' => '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>',
        'user_id' => 1000,
        'rating' => 1.4,
        'date_updated' => new CDbExpression('NOW()'),
    ),
    array(
        'id' => 2,
        'title' => 'Second Article with Title',
        'content' => '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>',
        'user_id' => 2000,
        'rating' => 30.2,
        'date_updated' => '2013-01-01 00:00:00',
    ),
    array(
        'id' => 3,
        'title' => 'Third Article with Title',
        'content' => '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>',
        'user_id' => 3000,
        'rating' => 0.4,
        'date_updated' => new CDbExpression('NOW()'),
    ),
    array(
        'id' => 4,
        'title' => 'Fourth Article with Title',
        'content' => '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>',
        'user_id' => 4000,
        'rating' => 0,
        'date_updated' => new CDbExpression('NOW()'),
    ),
);