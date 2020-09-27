<?php
/**
 * @license MIT
 * @author denis909
 * @link https://denis909.spb.ru
 */
namespace denis909\yii;

class CascadeFilesystemComponent extends \yii\base\Component
{

    public $aliases = [];

    public function init()
    {
        parent::init();

        foreach($this->aliases as $alias => $paths)
        {
            foreach($paths as $path)
            {
                CascadeFilesystem::setAlias($alias, $path);
            }
        }
    }

}