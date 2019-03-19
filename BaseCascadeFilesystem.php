<?php
/**
 * @copyright Copyright (c) denis909 2018
 * @license MIT
 * @author denis909
 * @link http://denis909.spb.ru
 */
namespace denis909\yii;

use Yii;
use yii\helpers\ArrayHelper;

abstract class BaseCascadeFilesystem extends \yii\base\Component
{

    public static $configFile = '@common/config/modules';

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

    public static function findFiles(string $file, $aliases = null)
    {
        if ($aliases === null)
        {
            $aliases = require Yii::getAlias(static::$configFile)/* . '.php'*/; 
        }

        $return = [];
        
        foreach($aliases as $key => $alias)
        {
            $filename = Yii::getAlias($alias) .'/' . $file/* . '.php'*/;

            if (is_file($filename))
            {
                $return[] = $filename;
            }
        }

        return $return;
    }

    public static function requireOnce(string $file, $aliases = null)
    {
        $files = static::findFiles($file, $aliases);

        foreach($files as $filename)
        {
            require_once($filename);
        }
    }

    public static function require(string $file, $aliases = null)
    {
        $files = static::findFiles($file, $aliases);

        foreach($files as $filename)
        {
            require($filename);
        }
    }

    public static function mergeArray(string $file, $aliases = null)
    {
        $files = static::findFiles($file, $aliases);

        foreach($files as $filename)
        {
            $return = ArrayHelper::merge($return, require $filename);
        }

        return $return;
    }

	public static function mergeConfig(string $file, array $config = [], $aliases = null)
	{
		$filename = __DIR__ . '/' . $file/* . '.php'*/;

		if (is_file($filename))
		{
			$return = require $filename;
		}
		else
		{
			$return = [];
		}

        return ArrayHelper::merge(
            $return, 
            static::mergeArray($file, $aliases), 
            $config
        );
	}

    public static function mergeContent(string $file, string $devider = "\n", $aliases = null)
    {
        $return = '';

        $files = static::findFiles($file, $aliases);

        foreach($files as $i => $filename)
        {
            if ($i > 0)
            {
                $return .= $devider;
            }

            $return .= file_get_contents($filename);
        }
        
        return $return;
    }

    // DEPRECATED

    public static function collect(string $file, array $config = [], $aliases = null)
    {
        return static::mergeConfig($file . '.php', $config, $aliases);
    }

}