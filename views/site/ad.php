<?php

use app\controllers;
use yii\helpers\Html;

$this->title = 'Ad';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="container" style="background: ;">

    <?php

    if (isset($adData)){
        echo "<h3>" . $adData['headline'] . "</h3>";
        echo "<h6>" . $adData['description'] . "</h6>";
        echo "<h4><b>" . $adData['price'] . "</b></h4>";
        echo "<i>" . $adData['category'] . "</i>";
        echo "<br>";
        echo "<i>" . $adData['city'] . "</i>";
        echo "<p>" . $adData['date'] . "</p>";

    } else {
        echo "<h3>No such advertisement...</h3>";
    }

    ?>

</div>

