<?php

use yii\helpers\Html;
use \yii\widgets\ActiveForm;
?>

<div class="col-lg-4">

<?php
    $form = ActiveForm::begin(['class' => 'form-horizontal']);
?>

<h3>Регистрация нового пользователя</h3>

<?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
<?= $form->field($model, 'password')->passwordInput()->label('Пароль') ?>

<div>
    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
</div>

<?php
    ActiveForm::end();
?>
    <br>
    <?= Html::a('Войти', ['/site/login']) ?>
</div>
