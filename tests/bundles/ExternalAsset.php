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
class ExternalAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'http://twitter.github.com/bootstrap/assets/css/bootstrap.css',
        'http://twitter.github.com/bootstrap/assets/css/ui.css',
        'css/other.css',
    ];
    public $js = [
        'js/main.js',
        'ftp://maps.google.com/maps/api/js?sensor=false',
        '//maps.google.com/maps/api/js?sensor=false',
        'js/other.js',
    ];
    public $depends = [
    ];
}
