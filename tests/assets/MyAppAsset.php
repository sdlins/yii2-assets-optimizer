<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace slinstj\AssetsOptimizer\tests\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MyAppAsset extends AssetBundle
{
    public $sourcePath = __DIR__;
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/other.css'
    ];
    public $js = [
        'js/main.js',
    ];
    public $depends = [
    ];
}
