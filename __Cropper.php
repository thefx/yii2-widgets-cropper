<?php
/**
 * Created by PhpStorm.
 * User: gerasinig
 * Date: 22.09.15
 * Time: 13:11
 */

namespace app\widgets\cropper;

use yii\base\Widget;
use yii\bootstrap\Modal;

class Cropper extends Widget{

    public function run(){
        $this->registerClientScript();
    }
    /**
     * Registers the client script required for the plugin
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        Asset::register($view);
    }
} 