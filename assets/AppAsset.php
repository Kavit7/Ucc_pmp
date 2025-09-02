<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
<<<<<<< HEAD
        'css/site.css',
=======
        'css/pmp.css',
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
<<<<<<< HEAD
        'yii\bootstrap5\BootstrapAsset'
=======
        'yii\bootstrap5\BootstrapAsset',
         'yii\bootstrap5\BootstrapPluginAsset',
>>>>>>> 1ebaa078084e2cc883cf30cc269da920b766552f
    ];
}
