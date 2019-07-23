<?php


use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'TADLE - the future of public advertisement';

?>

<div class="site-index">

    <div class="body-content">

        <?php
            echo Html::beginForm('', 'get');
            echo Html::dropDownList('sort', $sortFilter, [3 => 'Сначала новые', 4 => 'Сначала старые']);
            echo Html::dropDownList('category', $categoryFilter, array_merge(['0' => 'Все категории'], getCategories()));
            echo Html::input('text', 'search', $searchFilter);
            echo Html::submitButton('Поиск');
            echo Html::endForm();
        ?>

        <div class="row">
            <?php

            foreach($models as $ad){


                    echo Html::beginTag('a', ['href' => \yii\helpers\Url::to(['site/ad', 'id' => $ad->id])]);

                    echo Html::beginTag('div', ['class' => 'col-lg-3', 'style' => 'background: ; ']);
                    echo Html::beginTag('div', ['class' => 'index-ad', 'style' => '']);
                    echo Html::beginTag('div', ['class' => '', 'style' => 'margin: 10px; background: ;']);

                    if (isset($ad->photo)){
                        echo Html::img(Url::to(['/site/showimage', 'filename' => $ad->photo->picture]), ['style' => 'width: 100%;']);
                    }

                    echo Html::tag('h2', $ad->headline, ['style' => 'color: ;']);
//                    echo Html::tag('p', $ad->description, ['style' => 'background: ;']);
                    echo Html::tag('p', $ad->price . ' руб.', ['style' => 'font-size: 20px; font-weight: bold;']);
                    echo Html::tag('p', getCategories()[$ad->category] . ' / ' . $ad->city, ['style' => 'text-align: right;']);
                    echo Html::tag('p', $ad->date, ['style' => 'text-align: right;']);

                    echo Html::endTag('div');
                    echo Html::endTag('div');
                    echo Html::endTag('div');
                    echo Html::endTag('a');

                }

            ?>

        </div>

        <div>
            <?php

            echo \yii\widgets\LinkPager::widget([
            'pagination' => $pages,
            ]);

            ?>
        </div>

    </div>
</div>
