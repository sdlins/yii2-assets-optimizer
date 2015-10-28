<?php
namespace slinstj\assets\optimizer;

use yii\helpers;

/**
 * A modified View class capable of optimizing (minify and combine) assets bundles.
 *
 * @author Sidney Lins (slinstj@gmail.com)
 */
class AssetsOptimizerView extends \yii\web\View
{
    const TYPE_ALL = 1;
    const TYPE_CSS = 2;
    const TYPE_JS = 3;

    /** @var bool */
    public $enableOptimizer= true;

    /** @var array */
    public $typesToOptimize = [self::TYPE_ALL];

    /** @var string path alias to web base (in url) */
    public $webPath = '@web';

    /** @var string path alias to web base (absolute) */
    public $webrootPath = '@webroot';

    /** @var string path where to publish optimized css file(s) */
    public $optimizedCssPath = '@webroot/web/css';

    /** @var string path where to publish optimized css file(s) */
    public $optimizedJsPath = '@webroot/web/js';

    /** @var array js positions that should not be minified */
    public $jsPositionsToSkip = [];

    /**
     * @throws \rmrevin\yii\minify\Exception
     */
    public function init()
    {
        parent::init();

        /**
         * @todo Check or prepares directory structure.
         */
    }

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


    /**
     * @return self
     */
    protected function optimizeJs()
    {
        return $this;
    }
}