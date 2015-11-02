<?php
namespace slinstj\AssetsOptimizer;

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

    /**
     * @inheritdoc
     */
    public function endPage($ajaxMode = false)
    {
        $this->trigger(self::EVENT_END_PAGE);

        $content = ob_get_clean();

        /**
         * @todo Register AssetBundles.
         */

        /**
         * @todo Optimize assets.
         */

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
        return $this;
    }
}