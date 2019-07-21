<?php

use app\controllers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Create advertisment';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="container" style="background: ;">

<?php

if (Yii::$app->session->hasFlash('create-success'))
    echo "<div class=\"alert alert-success\" role=\"alert\">" . Yii::$app->session->getFlash('create-success') . "</div>";

if (Yii::$app->session->hasFlash('create-failed'))
    echo "<div class=\"alert alert-danger\" role=\"alert\">" . Yii::$app->session->getFlash('create-failed') . "</div>";



if (Yii::$app->user->identity){


//    echo Html::img('uploads/' . $userData['avatar'], ['alt' => 'yo, its my avatar', 'width' => 200]);


    $form = ActiveForm::begin(['class' => 'form-horizontal']);

    echo Html::tag('i', 'Добавить несколько фотографий можно будет после создания объявления');

    echo $form->field($picModel, 'picture')->fileInput();

    $model->city = \app\models\UserTable::findOne([ 'id' => Yii::$app->user->identity->getId() ])->city;

    echo $form->field($model, 'headline')->textInput()->label('Название');
    echo $form->field($model, 'price')->textInput()->label('Цена');
    echo $form->field($model, 'city')->dropDownList(getCities());

//    echo $form->field($model, 'category')->textInput()->label('Категория');

    echo $form->field($model, 'category')->dropDownList(getCategories());

    echo $form->field($model, 'description')->textarea()->label('Описание');

    echo Html::submitButton('Создать', ['class' => 'btn btn-primary']);

    ActiveForm::end();
} else {
    echo "Чтобы создать объявление, " . Html::a('войдите', Url::to(['/site/login']));
}


?>

</div>

