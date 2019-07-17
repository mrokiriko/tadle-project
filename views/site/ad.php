<?php

use app\controllers;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Ad';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="container" style="background: ;">

    <?php

    if (isset($adData)){

        if (isset($pictures)){
            foreach($pictures as $picture) {
                echo "<img src=" . Url::to(['/site/showimage', 'filename' => $picture->picture]) . " style='width: 200px;'>";
            }
        }

        echo "<h3>" . $adData['headline'] . "</h3>";
        echo "<h6>" . $adData['description'] . "</h6>";
        echo "<h4><b>" . $adData['price'] . " руб." . "</b></h4>";
        echo "<i>" . getCategories()[$adData['category']] . " / " . $adData['city'] . "</i>";
        echo "<br>";
        echo "<p>" . $adData['date'] . "</p>";
        echo "<p>" . "Контакты: " . $user['phone'] . ", " . $user['email'] . "</p>";
        echo Html::a($user['name'], ['site/profile', 'id' => $user['id']]);

    } else {
        echo "<h3>No such advertisement...</h3>";
    }

    ?>

</div>

