<?php
/* @var $this \yii\web\View */

use slinstj\AssetsOptimizer\tests\assets\MyAppAsset;

MyAppAsset::register($this);
?>
<html>
    <head>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>

        <?= $data ?>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
