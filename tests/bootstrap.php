<?php


define('ROOT', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'));

require ROOT.'/../../framework/yii.php';

require ROOT.'/sphinxapi.php';

require ROOT.'/enums/ESphinxGroup.php';
require ROOT.'/enums/ESphinxMath.php';
require ROOT.'/enums/ESphinxRank.php';
require ROOT.'/enums/ESphinxSort.php';

require ROOT.'/ESphinxException.php';
require ROOT.'/ESphinxSearchCriteria.php';
require ROOT.'/ESphinxQuery.php';
