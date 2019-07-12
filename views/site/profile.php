<?php

use yii\widgets\ActiveForm;$this->title = 'Profile';
$this->params['breadcrumbs'][] = $this->title;

?>


<?php

    if ($userData['id'] == $loginInfo['id']){

        $fields = ['name', 'city', 'about', 'phone'];
        foreach($fields as $field){
            $model->$field = $userData[$field];
        }

        $form = ActiveForm::begin(['class' => 'form-horizontal']);
        echo "<h4>Отредактировать информацию</h4>";

        echo $form->field($model, 'name')->textInput();
        echo $form->field($model, 'city')->textInput();
        echo $form->field($model, 'phone')->textInput();
        echo $form->field($model, 'about')->textInput()->label('Описание');

        echo '<div> <button type="submit" class="btn btn-primary">Сохранить</button> </div>';

        ActiveForm::end();

    } else if (isset($userData)){
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
