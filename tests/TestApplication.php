<?php

class TestApplication extends CConsoleApplication
{
	public function __construct($config=null)
	{
		Yii::setApplication(null);
		clearstatcache();
		parent::__construct($config);
	}

	public function reset()
	{
		$this->removeDirectory($this->getRuntimePath());
		$this->removeDirectory($this->getAssetPath());
	}

	protected function removeDirectory($path)
	{
		if(is_dir($path) && ($folder=@opendir($path))!==false)
		{
			while($entry=@readdir($folder))
			{
				if($entry[0]==='.')
					continue;
				$p=$path.DS.$entry;
				if(is_dir($p))
					$this->removeDirectory($p);
				@unlink($p);
			}
			@closedir($folder);
		}
	}

	public function getAssetPath()
	{
		return __DIR__.DS.'assets';
	}

	public function getRuntimePath()
	{
		return __DIR__.DS.'runtime';
	}

	public function getBasePath()
	{
		return __DIR__;
	}

	public function setBasePath($value)
	{
	}

	public function loadGlobalState()
	{
		parent::loadGlobalState();
	}

	public function saveGlobalState()
	{
		parent::saveGlobalState();
	}
}
