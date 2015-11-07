<?php
namespace slinstj\AssetsOptimizer;

use MatthiasMullie\Minify;
use yii\helpers\FileHelper;
use yii\helpers\Html;

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
    /**
     * @var string Path where optimized files will be published in. If you change this,
     * you *must* change [[publishUrl]] accordingly.
     * Optional. Defaults to '@webroot/yao'.
     */
    public $publishPath = '@webroot/yao';
    /**
     * @var string Web acessible Url where optimized files will be published in.
     * *Must* be in according to [[publishPath]].
     * Optional. Defaults to '@web/yao'.
     */
    public $publishUrl = '@web/yao';

    public function init()
    {
        parent::init();
        $this->publishPath = \Yii::getAlias($this->publishPath);
        $this->publishUrl = \Yii::getAlias($this->publishUrl);
    }

    /**
     * @inheritdoc
     */
    public function endPage($ajaxMode = false)
    {
        $this->trigger(self::EVENT_END_PAGE);

        $content = ob_get_clean();

        if ($this->minify === true) {
            $this->optimizeCss();
            $this->optimizeJs();
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

    protected function optimizeJs()
    {
        foreach ($this->jsFiles as $jsPosition => $files) {
            $result = $this->minifyFiles(array_keys($files), 'js', $jsPosition);
            $this->saveOptimizedJsFile($result, $jsPosition);
        }
    }

    protected function minifyFiles($fileUrls, $type, $jsPosition = self::POS_HEAD)
    {
        $min = ($type = strtolower($type)) === 'css' ? new Minify\CSS() : new Minify\JS;
        foreach ($fileUrls as $filePath) {
            if(!$this->isExternalSchema($filePath)) {
                $resolvedPath = $this->resolvePath($filePath);
                $min->add($resolvedPath);
                if($type === 'css'){
                    unset($this->cssFiles[$filePath]);
                }else {
                    unset($this->jsFiles[$jsPosition][$filePath]);
                }
            }
        }
        return $min->minify();
    }

    protected function isExternalSchema($path)
    {
        $schemas = ['http://', 'https://', 'ftp://', '//'];
        $mainRegex = '((' . implode('|', $schemas) . ').*?)';
        preg_match($mainRegex, $path, $matches);
        return isset($matches[1]);
    }

    protected function resolvePath($path)
    {
        $webroot = \Yii::getAlias('@webroot');
        $web = str_replace(\Yii::getAlias('@web'), '', $path);
        return realpath(FileHelper::normalizePath($webroot . DIRECTORY_SEPARATOR . $web));
    }

    protected function saveOptimizedCssFile($content)
    {
        $finalPath = $this->saveFile($content, $this->publishPath, 'css');
        $finalUrl = $this->publishUrl . DIRECTORY_SEPARATOR . basename($finalPath);
        $this->cssFiles[$finalPath] = Html::cssFile($finalUrl);
    }

    protected function saveOptimizedJsFile($content, $jsPosition)
    {
        $finalPath = $this->saveFile($content, $this->publishPath, 'js');
        $finalUrl = $this->publishUrl . DIRECTORY_SEPARATOR . basename($finalPath);
        $this->jsFiles[$jsPosition][$finalPath] = Html::jsFile($finalUrl);
    }

    protected function saveFile($content, $filePath, $ext)
    {
        $filename = substr(sha1($content), 0, 15) . '.yao.' . $ext;
        FileHelper::createDirectory($filePath);
        $finalPath = $filePath . DIRECTORY_SEPARATOR . $filename;

        if (is_file($finalPath) && filesize($finalPath)) {
            return $finalPath;
        }

        if (file_put_contents($finalPath, $content, LOCK_EX) !== false) {
            return $finalPath;
        } else {
            throw new \Exception("It was not possible to save the file '$finalPath'.");
        }

    }
}