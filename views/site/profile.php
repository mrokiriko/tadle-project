<?php

use yii\widgets\ActiveForm;
$this->title = 'Profile';
$this->params['breadcrumbs'][] = $this->title;

use yii\helpers\Html;

?>


<?php

    if ($userData['id'] == $loginInfo['id']){

        $fields = ['name', 'city', 'about', 'phone'];
        foreach($fields as $field){
            $model->$field = $userData[$field];
        }

        if (isset($userData['avatar'])){
            echo Html::img('uploads/' . $userData['avatar'], ['alt' => 'yo, its my avatar', 'width' => 200]);
        }

//        echo Html::img('uploads/' . $userData['avatar'], ['alt' => 'yo, its my avatar', 'width' => 200]);

        $form = ActiveForm::begin(['class' => 'form-horizontal']);

        echo $form->field($model, 'avatar')->fileInput();

        echo "<h4>Отредактировать информацию</h4>";

        echo $form->field($model, 'name')->textInput();
        echo $form->field($model, 'city')->textInput();
        echo $form->field($model, 'phone')->textInput();
        echo $form->field($model, 'about')->textInput()->label('Описание');

        echo '<div> <button type="submit" class="btn btn-primary">Сохранить</button> </div>';

        ActiveForm::end();

    } else if (isset($userData)){
//        echo "<img src=" . $userData['avatar'] . "></img>";


        if (isset($userData['avatar'])){
            echo Html::img('uploads/' . $userData['id'] . '-avatar.jpg', ['alt' => 'yo, its my avatar', 'width' => 200]);
        }
//        echo Html::img('uploads/' . $userData['avatar'], ['alt' => 'yo, its my avatar', 'width' => 200]);

        echo "<h3>Profile name: ". $userData['name'] ."</h3>";
        echo "<h4>Email: ". $userData['email'] ."</h4>";
        echo "<p><i>City: ". $userData['city'] ."</i></p>";
        echo "<h6>about: ". $userData['name'] ."</h6>";
        echo "<p><b>phone: ". $userData['phone'] ."</b></p>";
        echo "<p><i>date: ". $userData['date'] ."</i></p>";
    } else {
        echo "No such user...";
    }
?>
