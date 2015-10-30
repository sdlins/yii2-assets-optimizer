<?php
use slinstj\assets\optimizer\tests\assets\AppAsset;

/* @var $this \yii\web\View */

AppAsset::register($this);
?>
<html>
    <head>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
