<?php
/**
 * Created by PhpStorm.
 * User: gerasinig
 * Date: 21.09.15
 * Time: 15:18
 */

namespace thefx\widgetsCropper;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $css = [
        'cropper.min.css',
        'cropper-styles.css'
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'cropper.min.js'
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }
}

