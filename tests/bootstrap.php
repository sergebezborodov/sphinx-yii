<?php

define('DS', DIRECTORY_SEPARATOR);
defined('YII_ENABLE_EXCEPTION_HANDLER') or define('YII_ENABLE_EXCEPTION_HANDLER', false);
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', false);
defined('YII_DEBUG') or define('YII_DEBUG', true);

$_SERVER['SCRIPT_NAME']     = '/' . basename(__FILE__);
$_SERVER['SCRIPT_FILENAME'] = __FILE__;


define('ROOT', realpath(dirname(__FILE__).DS.'..'));

require ROOT.'/../../framework/yii.php';

// TODO: change it to sphinxapi-2.1.1.php if you are using beta version
require ROOT.'/sphinxapi-2.0.9.php';

require ROOT.'/enums/ESphinxGroup.php';
require ROOT.'/enums/ESphinxMatch.php';
require ROOT.'/enums/ESphinxRank.php';
require ROOT.'/enums/ESphinxSort.php';

require ROOT.'/ESphinxException.php';
require ROOT.'/ESphinxSearchCriteria.php';
require ROOT.'/ESphinxQuery.php';
require ROOT.'/ESphinxResult.php';

require ROOT . '/ESphinxBaseConnection.php';
require ROOT . '/ESphinxApiConnection.php';
require ROOT . '/ESphinxMysqlConnection.php';

require ROOT . '/ql/ESphinxQlDbConnection.php';
require ROOT . '/ql/ESphinxQlCommandBuilder.php';
require ROOT . '/ql/ESphinxQlCriteria.php';

require 'TestApplication.php';
Yii::import('system.test.*');


$config = require 'config.php';
$local  = require 'config-local.php';

$config = CMap::mergeArray($config, $local);

new TestApplication($config);
