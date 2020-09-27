<?php
/**
 * @license MIT
 * @author denis909
 * @link http://denis909.spb.ru
 */
namespace denis909\yii;

use Yii;

class CascadeFilesystem
{

    public static $pathMap = [];

    public static function setAlias($alias, $path)
    {
        static::$pathMap[$path] = $alias; 
    }

    public static function autoload($class)
    {
        foreach(static::$pathMap as $path => $alias)
        {
            $segments = explode("\\", $class);

            $className = array_pop($segments);

            $classNamespace = implode("\\", $segments);

            $classNamespaceAlias = '@' . str_replace("\\", '/', $classNamespace);

            if ($classNamespaceAlias == $alias)
            {
                if (strpos($path, '@') !== false)
                {
                    $filename = Yii::getAlias($path) . '/' . $className . '.php';
                }
                else
                {
                    $filename = $path . '/' . $className . '.php';
                }

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