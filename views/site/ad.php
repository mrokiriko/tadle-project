<?php

use app\controllers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Ad';
$this->params['breadcrumbs'][] = $this->title;
?>

<script>
    function deleteImage(url, file) {
        $.ajax({
            type: 'POST',
            cache: false,
            url: url,
            success: function(response){
                var link = 'div[id="' + file + '"]';
                $(link).remove();
            },
            error: function (err) {
                console.log(err)
            }
        });
    }

    $(document).ready(function(){

        $("img").click(function (){
            var modal = document.getElementById("popup-img-modal");
            if (modal.style.display != "block") {
                modal.style.display = "block";
            } else {
                modal.style.display = "none";
            }
            document.getElementById("modal-img").src = this.src;
        });

    });

</script>

<!-- The Modal -->
<div id="popup-img-modal" class="modal">
    <img class="modal-content" id="modal-img">
    <!--    <div id="caption"></div>-->
</div>

<div class="container" style="background: ;">

    <?php

    if (Yii::$app->session->hasFlash('update-success'))
        echo "<div class=\"alert alert-success\" role=\"alert\">" . Yii::$app->session->getFlash('update-success') . "</div>";
    if (Yii::$app->session->hasFlash('update-failed'))
        echo "<div class=\"alert alert-danger\" role=\"alert\">" . Yii::$app->session->getFlash('update-failed') . "</div>";

    if (isset($adData) && ($adData->userId == Yii::$app->user->getId())) {

        echo Html::beginTag('div', ['class' => 'container']);
        echo Html::beginTag('div', ['class' => 'row']);
        foreach ($pictures as $picture){


            echo Html::beginTag('div', ['class' => 'col', 'id' => $picture->picture]);
            echo Html::img(Url::to(['/site/showimage', 'filename' => $picture->picture]), ['style' => 'height: 200px; width: 200px;']);
            echo Html::button('Удалить фото', [
                'id' => 'link-del',
                'onclick' => 'deleteImage("' .
                    Url::to(['/site/deleteimage', 'picture' => $picture->picture]) . '", "' . $picture->picture .'");'
            ]);
            echo Html::endTag('div');

        }
        echo Html::endTag('div');
        echo Html::endTag('div');

        $photoModel = new \app\models\PhotoTable();

        echo Html::beginTag('div', ['class'=> '', 'style' => '']);

        $adPhotoForm = ActiveForm::begin(['class' => 'form-horizontal']);

        echo '<br>';

        echo $adPhotoForm->field($photoModel, 'picture')->fileInput()->label(false);

        echo Html::submitButton('Добавить фото', ['class' => 'btn btn-primary']);

        ActiveForm::end();

        echo Html::endTag('div');

        $form = ActiveForm::begin(['class' => 'form-horizontal']);

        $attributes = ['status', 'headline', 'price', 'city', 'category', 'description'];
        foreach($attributes as $attribute){
            $adModel->$attribute = $adData[$attribute];
        }

        echo $form->field($adModel, 'headline')->textInput()->label('Название');
        echo $form->field($adModel, 'status')->checkbox([], false)->label('Статус');
        echo $form->field($adModel, 'price')->textInput()->label('Цена');
        echo $form->field($adModel, 'city')->dropDownList(getCities());
        echo $form->field($adModel, 'category')->dropDownList(getCategories());
        echo $form->field($adModel, 'description')->textarea()->label('Описание');

        echo Html::submitButton('Обновить', ['class' => 'btn btn-primary']);

        ActiveForm::end();

    } else if (isset($adData)){

        echo Html::beginTag('div', ['class' => 'row']);

        echo Html::beginTag('div', ['class' => 'col-lg-8', 'style' => 'background: ;']);

        if (isset($pictures)){
            foreach($pictures as $picture) {
                echo "<img src=" . Url::to(['/site/showimage', 'filename' => $picture->picture]) . " style='width: 200px; max-height: 300px;'>";
            }
        }

        echo "<h3>" . $adData['headline'] . "</h3>";
        echo "<h6>" . $adData['description'] . "</h6>";
        echo "<h4><b>" . $adData['price'] . " руб." . "</b></h4>";
        echo "<i>" . getCategories()[$adData['category']] . " / " . getCities()[$adData['city']] . "</i>";
        echo "<br>";
        echo "<p>" . $adData['date'] . "</p>";

        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-lg-4', 'style' => 'background: ;']);

        if (isset($user['avatar'])){
            echo Html::img(Url::to(['uploads/' . $user['avatar']]), ['alt' => 'yo, its my avatar', 'width' => 200]);
        }

        echo '<br>';

        echo Html::a($user['name'] . ' (' . $userCount . ')', ['site/profile', 'id' => $user['id']]);
        echo Html::tag('p', 'На сайте с ' . $user['date']);
        echo Html::tag('p', $user['about']);
        echo Html::tag('p', "Контакты: +7" . $user['phone'] . ", " . $user['email']);

        echo Html::endTag('div');
        echo Html::endTag('div');


    } else {
        echo "<h3>No such advertisement...</h3>";
    }

    ?>

</div>

