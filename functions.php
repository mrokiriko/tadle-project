<?php



function getCategories(){
//    $categoriesDeclaration
    return [
        0 => 'Недвижимость',
        1 => 'Транспорт',
        2 => 'Личные вещи',
        3 => 'Хобби и отдых',
        4 => 'Услуги',
        5 => 'Бытовая техника',
        6 => 'Другое',
    ];
}

function debug($arr){
    echo '<pre>' . print_r($arr, true). '</pre>';
}
