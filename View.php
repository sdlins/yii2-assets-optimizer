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
     * @var string Path where optimized css file will be published in. If you change this,
     * you *must* change [[optmizedCssPath]] accordingly.
     * Optional. Defaults to '@webroot/yao'.
     */
    public $optimizedCssPath = '@webroot/yao';
    /**
     * @var string Web acessible Url where optimized css file(s) will be published in. 
     * *Must* be in according to [[optmizedCssPath]].
     * Optional. Defaults to '@web/yao'.
     */
    public $optimizedCssUrl = '@web/yao';
    /**
     * @var string Path where optimized css file will be published in. If you change this,
     * you *must* change [[optmizedCssPath]] accordingly.
     * Optional. Defaults to '@webroot/yao'.
     */
    public $optimizedJsPath = '@webroot/yao';
    /**
     * @var string Web acessible Url where optimized css file(s) will be published in.
     * *Must* be in according to [[optmizedCssPath]].
     * Optional. Defaults to '@web/yao'.
     */
    public $optimizedJsUrl = '@web/yao';

    public function init()
    {
        parent::init();
        $this->optimizedCssPath = \Yii::getAlias($this->optimizedCssPath);
        $this->optimizedCssUrl = \Yii::getAlias($this->optimizedCssUrl);
        $this->optimizedJsPath = \Yii::getAlias($this->optimizedJsPath);
        $this->optimizedJsUrl = \Yii::getAlias($this->optimizedJsUrl);
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
        $finalPath = $this->saveFile($content, $this->optimizedCssPath, 'css');
        $finalUrl = $this->optimizedCssUrl . DIRECTORY_SEPARATOR . basename($finalPath);
        $this->cssFiles[$finalPath] = Html::cssFile($finalUrl);
    }

    protected function saveOptimizedJsFile($content, $jsPosition)
    {
        $finalPath = $this->saveFile($content, $this->optimizedJsPath, 'js');
        $finalUrl = $this->optimizedJsUrl . DIRECTORY_SEPARATOR . basename($finalPath);
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