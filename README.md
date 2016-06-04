Yii 2 Assets Optimizer
===========================

Fast and reliable unifier and compressor for CSS and JS files inside your Yii2 AssetBundles.
Uses internal server cache to speed up all the process, reducing the time wasting.

Report your issues
-------
[GitHub issues](https://github.com/slinstj/yii2-assets-optimizer/issues).

Installation
------------
```bash
composer require "slinstj/yii2-assets-optimizer:>=0.1-stable"
```

Configuration/Usage
---------
```php
<?
return [
    // ...
    'components' => [
        // ...
        'view' => [
            'class' => '\slinstj\assets\optimizer\View',
        ]
    ]
];
```

Additional Options
---------
```php
<?
return [
    // ...
    'components' => [
        // ...
        'view' => [
            'class' => '\slinstj\assets\optimizer\View',
            'minify' => true, // Could be '!YII_DEBUG' for example.
            'publishPath' => '@webroot/yao', // Folder where optimized file(s) will be published in.
            'publishUrl' => '@web/yao', // Web acessible url. Must be in accord to 'publishPath'.
        ]
    ]
];
```

Next versions
---------
* To improve cache by using ChainedDependency - On change JS and CSS files, the optimized
file(s) will be regenerated automatically. For now, **you should clear the cache manually**.
*Please, check this to know how: [cache-flushing](http://www.yiiframework.com/doc-2.0/guide-caching-data.html#cache-flushing).*
* To use events instead of the own View object;

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/slinstj/yii2-assets-optimizer/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

