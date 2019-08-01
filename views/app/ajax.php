<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>


<?php


echo Html::beginForm(Url::to(['app/get-profile']), 'post');

echo Html::input('text', 'log', '123');
echo Html::input('password', 'pass', '123');
echo Html::submitButton('Push me');

echo Html::endForm();

?>
