<?php
use yii\bootstrap\Modal;

function modal($title,$w,$h){
    if($title && isset($w) && isset($h)){
        $text = 'Crop '.$w.'x'.$h;
        Modal::begin([
            'header' => '<h3>'.$text.'<span class="'.$title.'-img-error pull-right" style="color:red;"></span></h3>',
            'toggleButton' => ['label' => $text,'class'=>'btn'],
            'size'=>'modal-lg'
        ]);
        echo '<div id="'.$title.'" class="cropper-img-container">Загрузите сначала изображение.</div>';
        Modal::end();
    }
}
?>
<div style="margin: 15px 0">
<?php
if($cropConfig){
    if($cropConfig['defaultCrop']){
        modal('defaultCrop',$cropConfig['defaultCrop'][0],$cropConfig['defaultCrop'][1]);
    }

    if($cropConfig['crop']){
        foreach($cropConfig['crop'] as $item){
            modal($item[2],$item[0],$item[1]);
        }
    }
}

echo $cropInput;
?>
</div>
