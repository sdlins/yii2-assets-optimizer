<?php

namespace slinstj\AssetsOptimizer\tests;

use yii\web\AssetManager;
use slinstj\AssetsOptimizer\View;
use Yii;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-10-30 at 17:45:03.
 */
class ViewTest extends TestCase
{

    /**
     * @var AssetsOptimizerView
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockWebApplication();
        $this->object = new AssetsOptimizerView;
    }

    protected function tearDown()
    {
        parent::tearDown();
//        FileHelper::removeDirectory(Yii::getAlias('@runtime/assets'));
    }

    public function testRender()
    {
        $view = $this->mockView();
        $content = $view->renderFile('@yaotests/views/index.php', ['data' => 'Hello World!']);

        $this->assertEquals('test view Hello World!.', $content);
    }

    /**
     * @return View
     */
    protected function mockView()
    {
        return new View([
            'assetManager' => $this->mockAssetManager(),
        ]);
    }

    protected function mockAssetManager()
    {
        $assetDir = Yii::getAlias('@runtime/assets');
        if (!is_dir($assetDir)) {
            mkdir($assetDir, 0777, true);
        }

        return new AssetManager([
            'basePath' => $assetDir,
            'baseUrl' => '/assets',
        ]);
    }
}
