<?php

define('DS', DIRECTORY_SEPARATOR);
defined('YII_ENABLE_EXCEPTION_HANDLER') or define('YII_ENABLE_EXCEPTION_HANDLER', false);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', false);
defined('YII_DEBUG') or define('YII_DEBUG', true);

$_SERVER['SCRIPT_NAME']     = '/' . basename(__FILE__);
$_SERVER['SCRIPT_FILENAME'] = __FILE__;


define('ROOT', realpath(dirname(__FILE__).DS.'..'));

require ROOT.'/../../framework/yii.php';

require ROOT.'/sphinxapi.php';

require ROOT.'/enums/ESphinxGroup.php';
require ROOT.'/enums/ESphinxMath.php';
require ROOT.'/enums/ESphinxRank.php';
require ROOT.'/enums/ESphinxSort.php';

require ROOT.'/ESphinxException.php';
require ROOT.'/ESphinxSearchCriteria.php';
require ROOT.'/ESphinxQuery.php';

require ROOT . '/ESphinxBaseConnection.php';
require ROOT . '/ESphinxConnection.php';

require 'TestApplication.php';
Yii::import('system.test.*');


$config = require 'config.php';
$local  = require 'config-local.php';

$config = CMap::mergeArray($config, $local);

new TestApplication($config);
