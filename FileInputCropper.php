<?php
/**
 * Created by PhpStorm.
 * User: gerasinig
 * Date: 22.09.15
 * Time: 18:11
 */

namespace thefx\widgetsCropper;

use Yii;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\web\ServerErrorHttpException;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Надстройка над полем загрузки изобр. (FileInput). Добавляет функционал cropper.
 * */
class FileInputCropper extends InputWidget
{
    /**
     * @var Атрибут модели
     */
    public $cropAttribute;

    /**
     * @var Имя input
     */
    public $cropName;

    /**
     * Настройка кропа (можно взять из confg)
     * 'dir'=>'@frontend/web/upload/content/',
     * 'urlDir'=>'/upload/content/',
     * 'defaultCrop' => [100,75],
     * 'crop'=>[
     *   [850,0,'nw'],
     *   [850,480,'in']
     * ]
     * @var array
     */
    public $cropConfig = [];

    /**
     * @var id input в котором передается данные js cropper
     */
    private $cropIdInput;

    public $pluginOptions = [];

    public function init()
    {
        if (!$this->cropConfig) throw new ServerErrorHttpException('FileInputCropper :: Укажите cropConfig.');
        $this->registerClientScript();

        if ($this->cropAttribute) $this->cropIdInput = $this->cropAttribute;
        else $this->cropIdInput = $this->cropName;

        Yii::$app->view->registerJs("
            
            var dataCropper = {};
        
            function previewFile{$this->getId()}() {
                var wrapper = document.querySelector('#{$this->getId()}');
                var preview = wrapper.querySelector('.photo_preview');
                var file = wrapper.querySelector('input[type=file]').files[0];
               
                function cropImg(title,w,h){
                console.log(this);
                    var URL = window.URL || window.webkitURL;
                    blobURL = URL.createObjectURL(file);
                    var img = new Image();
                    img.src = blobURL;
                    wrapper.querySelector('.' + title).textContent = '';
                    wrapper.querySelector('.' + title).appendChild(img);
                    
                    cropOptions = {
//                        guides: true,
                        responsive: false,
                        
                        preview: preview,
                        center: true,
                        autoCropArea: 1,
                        background: false,
                        viewMode: 1,
                        minContainerWidth:868,
                        minContainerHeight:400,
                        crop: function() {
                            e = cropper.getData(true);
                            if (e.width < w ||  e.height < h) wrapper.querySelector('.'+ title +'-img-error').textContent = ('w:' + Math.round(e.width) + ' ' + 'h:' + Math.round(e.height));
                            else wrapper.querySelector('.'+ title +'-img-error').textContent = '';
                            dataCropper[title] = e;
                            wrapper.querySelector('#{$this->cropIdInput}').value = JSON.stringify(dataCropper);
                        },
                    };
                    
                    if(w != 0 && h != 0) cropOptions.aspectRatio = w / h;
                    var image = wrapper.querySelector('.' + title + ' img');
                    var cropper = new Cropper(image, cropOptions);
                    
//                    // Buttons
//                    wrapper.querySelector('.docs-toggles').onchange = function (event) {
//                        var e = event || window.event;
//                        var target = e.target || e.srcElement;
//                        var isCheckbox;
//                        var isRadio;
//                        
//                        if (target.tagName.toLowerCase() === 'label') {
//                            target = target.querySelector('input');
//                        }
//                        
//                        isCheckbox = target.type === 'checkbox';
//                        isRadio = target.type === 'radio';
//
//                        if (isCheckbox || isRadio) {
//                            if (isCheckbox) {
//                                cropOptions[target.name] = target.checked;
//                                cropBoxData = cropper.getCropBoxData();
//                                canvasData = cropper.getCanvasData();
//                    
//                                cropOptions.ready = function () {
//                                    console.log('ready');
//                                    cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
//                                };
//                            } else {
//                              cropOptions[target.name] = target.value;
//                              cropOptions.ready = function () {
//                                   console.log('ready');
//                              };
//                            }
//                    
//                            // Restart
//                            cropper.destroy();
//                            cropper = new Cropper(image, cropOptions);
//                        }
//                        console.log('a12');
//                    }
                }
               
                " . $this->cropJs() . "
            }
            
        ", View::POS_HEAD);

        parent::init();
    }

    public function run()
    {
        if ($this->hasModel() && $this->cropAttribute) {
            $cropInput = Html::activeHiddenInput($this->model, $this->cropAttribute, ['id' => $this->cropAttribute]);
        } elseif ($this->cropName) {
            $cropInput = Html::hiddenInput($this->cropName, '', ['id' => $this->cropName]);
        } else throw new ServerErrorHttpException('FileInputCropper :: Укажите cropAttribute (атрибут модели) или cropName (имя input).');

        return $this->render('index', [
            'cropInput' => $cropInput,
            'cropConfig' => $this->cropConfig,
            'model' => $this->model,
            'attributeName' => $this->attribute,
            'imagePreview' => $this->pluginOptions['imagePreview'] ? $this->pluginOptions['imagePreview'] : '',
            'imageUrl' => $this->pluginOptions['imageUrl'] ? $this->pluginOptions['imageUrl'] : '',
        ]);
    }

    public function registerClientScript()
    {
        $view = $this->getView();
        Asset::register($view);
    }

    /**
     * Устанавливает функцию js функцию cropImg в зависимости от настроек виджета (см. свойство cropConfig).
     * @return string
     */
    public function cropJs()
    {
        $cropJs = '';
        if ($this->cropConfig) {
            if ($this->cropConfig['defaultCrop'])
                $cropJs = "cropImg('defaultCrop'," . $this->cropConfig['defaultCrop'][0] . "," . $this->cropConfig['defaultCrop'][1] . ");";
            if ($this->cropConfig['crop']) {
                foreach ($this->cropConfig['crop'] as $item) {
                    $cropJs .= "cropImg('" . $item[2] . "'," . $item[0] . "," . $item[1] . ");";
                }
            }
        }
        return $cropJs;
    }

    function modal($title, $w, $h)
    {
        if (isset($w, $h) && $title) {
            $text = '<i class="fa fa-crop"></i> Crop ' . $w . 'x' . $h;
            Modal::begin([
                'title' => '<h3>' . $text . '<span class="' . $title . '-img-error pull-right" style="color:red;"></span></h3>',
                'toggleButton' => ['label' => $text, 'class' => 'btn btn-default'],
                'size' => 'modal-lg',
                'options' => ['class' => ''],
//                'footer' => '<div class="btn-group d-flex flex-nowrap docs-toggles" data-toggle="buttons">
//                  <label class="btn btn-primary">
//                    <input type="radio" class="sr-only" id="viewMode0" name="viewMode" value="0" checked="">
//                    <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="View Mode 0">
//                      VM0
//                    </span>
//                  </label>
//                  <label class="btn btn-primary active">
//                    <input type="radio" class="sr-only" id="viewMode1" name="viewMode" value="1">
//                    <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="View Mode 1">
//                      VM1
//                    </span>
//                  </label>
//                  <label class="btn btn-primary">
//                    <input type="radio" class="sr-only" id="viewMode2" name="viewMode" value="2">
//                    <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="View Mode 2">
//                      VM2
//                    </span>
//                  </label>
//                  <label class="btn btn-primary">
//                    <input type="radio" class="sr-only" id="viewMode3" name="viewMode" value="3">
//                    <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="View Mode 3">
//                      VM3
//                    </span>
//                  </label>
//                </div>',
            ]);
            echo '<div class="cropper-img-container ' . $title . '">Загрузите сначала изображение.</div><div class="clearfix"></div>';
            Modal::end();
        }
    }
} 