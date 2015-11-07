<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace slinstj\AssetsOptimizer\tests\bundles;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SourcePathAsset extends AssetBundle
{
    public $sourcePath = __DIR__;
    public $css = [
        'css/site.css',
        'css/other.css',
    ];
    public $js = [
        'js/main.js',
        'js/other.js',
    ];
    public $depends = [
    ];
}
