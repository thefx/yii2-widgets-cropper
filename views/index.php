<?php
/* @var array $cropConfig */
/* @var array $cropInput */
/* @var ActiveRecord $model */

/* @var string $attributeName */
/* @var string $imagePreview */
/* @var string $imageUrl */

use yii\db\ActiveRecord;
use yii\helpers\Html;
?>

<div class="well" id="<?= $this->context->getId() ?>">
    <?php if (!$model->{$attributeName}) : ?>
        <?= HTML::activeFileInput($model, $attributeName, ['accept' => 'image/*', 'onchange' => "previewFile{$this->context->getId()}()"]) ?>
        <div class="clearfix" style="margin: 15px 0 0">
            <div class="photo_preview"></div>
        </div>
        <div style="margin: 15px 0 0">
            <?php
            if ($cropConfig) {
                if ($cropConfig['defaultCrop']) {
                    $this->context->modal('defaultCrop', $cropConfig['defaultCrop'][0], $cropConfig['defaultCrop'][1]);
                }
                if ($cropConfig['crop']) {
                    foreach ($cropConfig['crop'] as $item) {
                        $this->context->modal($item[2], $item[0], $item[1]);
                    }
                }
            }
            echo $cropInput;
            ?>
        </div>
    <?php else : ?>
        <div class="thumbnail pull-left no-margin">
            <?php
                echo '<div class="image">' . $imagePreview . '</div>';
                echo Html::a('<i class="fa fa-times-circle"></i>', ['delete-photo', 'id' => $model->getPrimaryKey(), 'field' => $attributeName], [
                    'class' => 'btn-delete',
                    'data-confirm' => 'Удалить изображение?',
                    'data-method' => 'post',
                    'title' => 'Удалить изображение',
                ]);
            echo Html::a('<i class="fas fa-link"></i>', [$imageUrl], ['class' => 'btn-link', 'target' => '_blank']);
            ?>
        </div>
    <?php endif; ?>
    <div class="clearfix"></div>
</div>

