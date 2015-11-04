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
    public $optimizedCssPath = '@webroot/css';

    /**
     * @var string Optional. Url where optimized css file(s) were published in. If
     * not given, url will be formed by '@web' plus [[optimizedCssPath]] without any alias.
     * For example: [optimizedCssPath] = '@someAlias/path/to/myDir' and
     * [[optimizedCssUrl]] is null, url will be '@web/path/to/myDir'.
     */
    public $optimizedCssUrl;

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
        $result = $this->minifyFiles(array_keys($this->cssFiles), 'css');
        $this->saveOptimizedCssFile($result);
    }

    protected function minifyFiles($fileUrls, $type)
    {
        $min = ($type = strtolower($type)) === 'css' ? new Minify\CSS() : new Minify\JS;
        foreach ($fileUrls as $filePath) {
            $resolvedPath = $this->resolvePath($filePath);
            $min->add($resolvedPath);
            if($type === 'css') {
                unset($this->cssFiles[$filePath]);
            } else {
                unset($this->jsFiles[$filePath]);
            }
        }
        return $min->minify();
    }

    protected function resolvePath($path)
    {
        $basePath = \Yii::getAlias('@webroot');
        $baseUrl = str_replace(\Yii::getAlias('@web'), '', $path);
        $resolvedPath = realpath($basePath . DIRECTORY_SEPARATOR . $baseUrl);
        return $resolvedPath;
    }

    protected function saveOptimizedCssFile($content)
    {
        $finalPath = $this->saveFile($content, \Yii::getAlias($this->optimizedCssPath), 'css');

        if (!empty($this->optimizedCssUrl)) {
            if (! $this->isValidPath($this->optimizedCssUrl)) {
                throw new \yii\web\NotFoundHttpException("The 'optimizedCssUrl' ($realOptCssUrl) given is invalid.");
            }
            $finalUrl = \Yii::getAlias($this->optimizedCssPath);
        } else {
            preg_match('~^(@.*?)(\/|\\\\)(.*)~', $this->optimizedCssPath, $matches);
            $alias = isset($matches[1]) ? $matches[1] : '@web';
            $desiredDir = !isset($matches[2], $matches[3]) ?: implode('', [$matches[2], $matches[3]]);
            $finalUrl = $alias . $desiredDir . DIRECTORY_SEPARATOR . $filename;
        }

        $this->cssFiles[$finalPath] = \yii\helpers\Html::cssFile($finalUrl);
    }

    protected function saveFile($content, $filePath, $ext)
    {
        $filename = sha1($content) . '.' . $ext;
        \yii\helpers\FileHelper::createDirectory($filePath);
        $finalPath = $filePath . DIRECTORY_SEPARATOR . $filename;

        if (file_put_contents($finalPath, $content, LOCK_EX) !== false) {
            return $finalPath;
        } else {
            throw new \Exception("Was not possible to save the file '$finalPath'.");
        }

    }

    protected function isValidPath($path)
    {
        return !empty($path) && realpath(($realPath = \Yii::getAlias($path)));
    }
}