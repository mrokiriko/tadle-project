<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title = 'Profile';
$this->params['breadcrumbs'][] = $this->title;

use yii\helpers\Html;
?>


<div class="container">
    <div class="row">

<?php

//    debug(Yii::getVersion());
//    die();


    if ($userData['id'] == $loginInfo['id']){

        $fields = ['name', 'city', 'about', 'phone'];
        foreach($fields as $field){
            $model->$field = $userData[$field];
        }

        if (isset($userData['avatar'])){
            echo Html::img(Url::to(['uploads/' . $userData['avatar']]), ['alt' => 'yo, its my avatar', 'width' => 200]);

            if ($userData['avatar'] != getDefaultAvatar()){
                echo Html::beginForm('', 'post');
                echo Html::input('hidden', 'delete-avatar', $userData['avatar']);
                echo Html::submitButton('Удалить аватар');
                echo Html::endForm();
            }
        }


        $form = ActiveForm::begin(['class' => 'form-horizontal']);

        echo $form->field($model, 'avatar')->fileInput();

        echo "<h4>Отредактировать информацию</h4>";

        echo $form->field($model, 'name')->textInput()->label('Имя на сайте');
        echo $form->field($model, 'city')->dropDownList(getCities())->label('Выберите город');

        echo Html::label('Телефон для связи');

        echo Html::beginTag('div', ['class' => 'input-group']);

        echo Html::tag('span', '+7', ['class' => 'input-group-addon']);
        echo Html::activeTextInput($model, 'phone', ['class' => 'form-control', 'maxlength' => '10']);

        echo Html::endTag('div');

        echo $form->field($model, 'about')->textInput()->label('Описание');

        echo '<div> <button type="submit" class="btn btn-primary">Сохранить</button> </div>';

        ActiveForm::end();

    } else if (isset($userData)){

        if (isset($userData['avatar'])){
            echo Html::img(Url::to(['uploads/' . $userData['avatar']]), ['alt' => 'yo, its my avatar', 'width' => 200]);
        }

        echo "<h3>Profile name: ". $userData['name'] ."</h3>";
        echo "<h4>Email: ". $userData['email'] ."</h4>";
        echo "<p><i>City: ". getCities()[$userData['city']] ."</i></p>";
        echo "<h6>about: ". $userData['name'] ."</h6>";
        echo "<p><b>+7 ". $userData['phone'] ."</b></p>";
        echo "<p><i>date: ". $userData['date'] ."</i></p>";
    } else {
        echo "No such user...";
    }

    ?>


    <?php


if (count($userPosts) > 0){
        echo "<br><h4>Объявления пользователя: </h4>";
    } else {
        echo "<br><h4>У пользователя нет объявлений...</h4>";
    }

    echo Html::beginForm(Url::to(['site/profile', 'id' => $userData['id']]), 'get');
    echo Html::dropDownList('status', $statusFilter, [1 => 'Активные', 0 => 'Неактивные']);
    echo Html::dropDownList('sort', $sortFilter, [3 => 'Сначала новые', 4 => 'Сначала старые']);
    echo Html::dropDownList('category', $categoryFilter, array_merge(['0' => 'Все категории'], getCategories()));
    echo Html::dropDownList('city', $cityFilter, array_merge(['0' => 'Все города'], getCities()));
    echo Html::input('text', 'search', $searchFilter);
    echo Html::submitButton('Поиск');
    echo Html::endForm();


$status = ['Неактивное', 'Активное'];

    foreach ($userPosts as $post){

        echo Html::beginTag('a', ['href' => \yii\helpers\Url::to(['site/ad', 'id' => $post->id])]);

        echo Html::beginTag('div', ['class' => 'col-lg-3', 'style' => 'background: ; ']);
        echo Html::beginTag('div', ['class' => 'index-ad', 'style' => '']);
        echo Html::beginTag('div', ['class' => '', 'style' => 'margin: 10px; background: ;']);
        echo Html::tag('p', $status[$post->status], ['style' => 'font-size: 15px; font-weight: bold;']);


        if (isset($post->photo))
            echo Html::img(Url::to(['/site/showimage', 'filename' => $post->photo->picture]), ['style' => 'width: 100%; max-height: 300px;']);

        echo Html::tag('h2', $post->headline, ['style' => 'color: ;']);
        echo Html::tag('p', $post->price . ' руб.', ['style' => 'font-size: 20px; font-weight: bold;']);
        echo Html::tag('h5', $post->description, ['style' => 'color: ;']);
        echo Html::tag('p', getCategories()[$post->category] . ' / ' . getCities()[$post->city], ['style' => 'text-align: right;']);
        echo Html::tag('p', $post->date, ['style' => 'text-align: right;']);


        echo Html::endTag('div');
        echo Html::endTag('div');
        echo Html::endTag('div');
        echo Html::endTag('a');
    }

?>
    </div>
</div>

<div class="container">
    <?php

    echo \yii\widgets\LinkPager::widget([
        'pagination' => $postsPagination,
    ]);

    ?>
</div>