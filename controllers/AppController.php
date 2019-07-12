<?php


namespace app\controllers;

use yii\web\Controller;

class AppController extends Controller {

    public function debug($arr){
        echo '<pre>' . print_r($arr, true). '</pre>';
    }

    public function getFunny($a){
        echo '<h3>' . 'u so funny, omg' . $a . '</h3>';
    }

}
