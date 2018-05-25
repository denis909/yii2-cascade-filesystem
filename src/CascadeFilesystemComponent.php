<?php
/**
 * @copyright Copyright (c) 2018 denis909
 * @license https://github.com/denis909/yii2-cascade-filesystem/blob/master/LICENSE
 * @link http://denis909.spb.ru
 */

namespace denis909\yii;

use Yii;

class CascadeFilesystemComponent extends \yii\base\Component
{

	public $aliases = [];

	public function init()
	{
		parent::init();

		spl_autoload_register(array($this, 'autoload'), true, false);
	}

	public function autoload($class)
	{
		foreach($this->aliases as $fromNamespace => $toNamespace)
		{
			$segments = explode("\\", $class);

			$className = array_pop($segments);

			$classNamespace = implode("\\", $segments);

			$classNamespaceAlias = '@' . str_replace("\\", '/', $classNamespace);

			if ($classNamespaceAlias == $toNamespace)
			{
				$filename = Yii::getAlias($fromNamespace) . '/' . $className . '.php';

				if (is_file($filename))
				{
					require_once $filename;

					$exists = class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false);	
				
					if ($exists)
					{
						return true;
					}
				}				
			}
		}

		return false;
	}	

}