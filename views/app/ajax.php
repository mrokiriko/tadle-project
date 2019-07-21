<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<script>
function deleteImage(url, file) {
    $.ajax({
        type: 'POST',
        cache: false,
        url: url,
        success: function(response){
            // alert('yeeeeeeah: ' + response);
            var link = 'div[id="' + file + '"]';
            $(link).remove();
        },
        error: function (err) {
            // alert('error popped up: ' + err);
            console.log(err)
        }
    });
}
</script>

<p>
    <?= $error == '' ? 'suck a dick' : $error ?>
    <?= Url::to(['/app/ajax']) ?>

</p>

<?php

$pictures = array_reverse($pictures);

echo Html::beginTag('div', ['class' => 'container']);
echo Html::beginTag('div', ['class' => 'row']);
foreach ($pictures as $picture){


    echo Html::beginTag('div', ['class' => 'col', 'id' => $picture->picture]);
    echo Html::img(Url::to(['/site/showimage', 'filename' => $picture->picture]), ['style' => 'height: 200px; width: 200px;']);
    echo Html::button('click me to delete', [
        'id' => 'link-del',
        'onclick' => 'deleteImage("' .
            Url::to(['/app/deleteimage', 'picture' => $picture->picture]) . '", "' . $picture->picture .'");'
    ]);
    echo Html::endTag('div');

}
echo Html::endTag('div');
echo Html::endTag('div');

$form = ActiveForm::begin([
    'id' => 'add-photo-form',
    'options' => ['class' => 'form-horizontal'],
]);

?>

    <div class="form-group">
        <?= $form->field($photoModel, 'picture')->fileInput() ?>
        <?= Html::submitButton('Add photo', ['class' => 'btn btn-primary', 'onclick' => 'send();']) ?>
    </div>

<?php ActiveForm::end(); ?>