<?php
/**
 * @copyright Copyright (c) 2018 denis909
 * @license https://github.com/denis909/yii2-cascade-filesystem/blob/master/LICENSE
 * @author denis909
 * @link http://denis909.spb.ru
 */

namespace denis909\yii;

use Yii;
use yii\helpers\ArrayHelper;

class CascadeFilesystem
{

	public $pathMap = [];

	public function init()
	{
		parent::init();

		spl_autoload_register([$this, 'autoload'], true, false);
	}

	public function autoload($class)
	{
		foreach($this->pathMap as $fromNamespace => $toNamespace)
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

	public static function collect(string $file, array $default = [], $aliases = null)
	{
		$filename = __DIR__ . '/' . $file . '.php';

		if (is_file($filename))
		{
			$return = require $filename;
		}
		else
		{
			$return = [];
		}

		if ($aliases === null)
		{
			$aliases =  require Yii::getAlias('@common/config') . '/modules.php'; 
		}

		foreach($aliases as $key => $alias)
		{
			$filename = Yii::getAlias($alias) .'/' . $file . '.php';

			if (is_file($filename))
			{
		    	$return = ArrayHelper::merge($return, require $filename);
			}
		}

		if ($default)
		{
			return ArrayHelper::merge($return, $default);
		}

		return $return;
	}

}