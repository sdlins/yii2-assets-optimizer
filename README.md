Yii 2 Assets Optmizer
===========================

Unifies and compresses js and css files in your AssetBundles. Uses internal server cache
to speed up all the process, reducing the time wasting.

Report your issues
-------
[GitHub issues](https://github.com/slinstj/yii2-assets-optimizer/issues).

Installation
------------
```bash
composer require "slinstj/yii2-assets-optmizer:*"
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
			'class' => '\slinstj\assets\optmizier\View',
		]
	]
];
```
