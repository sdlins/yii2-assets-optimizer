<?php
namespace slinstj\AssetsOptimizer;

use MatthiasMullie\Minify;

/**
 * A modified View class capable of optimizing (minify and combine) assets bundles.
 * @author Sidney Lins (slinstj@gmail.com)
 */
class View extends \yii\web\View
{

    /** @var bool */
    public $minify = true;

    /** @var bool */
    public $combine = true;

    /** @var string path where to publish optimized css file(s) in */
    public $optimizedCssPath = '@webroot/web/css';

    /** @var string path where to publish optimized css file(s) in */
    public $optimizedCssUrl = '@web/css';

    /**
     * @inheritdoc
     */
    public function endPage($ajaxMode = false)
    {
        $this->trigger(self::EVENT_END_PAGE);

        $content = ob_get_clean();

        if ($this->minify === true) {
            $this->optimizeCss();
        }

        echo strtr(
            $content,
            [
                self::PH_HEAD => $this->renderHeadHtml(),
                self::PH_BODY_BEGIN => $this->renderBodyBeginHtml(),
                self::PH_BODY_END => $this->renderBodyEndHtml($ajaxMode),
            ]
        );

        $this->clear();
    }

    /**
     * @return self
     */
    protected function optimizeCss()
    {
        $min = new Minify\CSS();
        foreach (array_keys($this->cssFiles) as $filePath) {
            $min->add($filePath);
            unset($this->cssFiles[$filePath]);
        }
        $result = $min->minify();

        $filename = sha1($result) . ".css";
        $finalPath = \Yii::getAlias($this->optimizedCssPath) . DIRECTORY_SEPARATOR . $filename;

        $finalUrl = sprintf('%s/%s', \Yii::getAlias($this->optimizedCssUrl), $filename);

        file_put_contents($finalPath, $result, LOCK_EX);

        $this->cssFiles[$finalPath] = \yii\helpers\Html::cssFile($finalUrl);
    }
}