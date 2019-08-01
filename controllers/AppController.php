<?php


namespace app\controllers;

use Yii;
use app\models\AdTable;
use app\models\PhotoTable;
use yii\web\Controller;
use yii\web\UploadedFile;

class AppController extends Controller {


    public function debug($arr){
        echo '<pre>' . print_r($arr, true). '</pre>';
    }

    public function getFunny($a){
        echo '<h3>' . 'u so funny, omg' . $a . '</h3>';
    }


    public function actionAjax(){
        return $this->render('ajax');
    }

    public function actionGetProfile() {

        $users = [
            ['id' => 1, 'name' => 'Anatoly Simonov', 'log' => 'anat03', 'pass' => 'qwerty', 'token' => '1'],
            ['id' => 2, 'name' => 'Igor Michalkov', 'log' => 'director-the-best', 'pass' => '123123', 'token' => '2'],
            ['id' => 3, 'name' => 'Gosha Shilov', 'log' => 'gsh', 'pass' => 'cvbn', 'token' => '3'],
            ['id' => 4, 'name' => 'Masha Neveruushaya', 'log' => 'neverland', 'pass' => '0987654321', 'token' => '4'],
            ['id' => 5, 'name' => 'Vera Pavlovna', 'log' => 'verap', 'pass' => '132456', 'token' => '5'],
            ['id' => 6, 'name' => 'Nadezhda Menshikova', 'log' => 'diamond-flower', 'pass' => 'kremlindont', 'token' => '6'],
        ];

        $log = Yii::$app->request->post('log');
        $pass = Yii::$app->request->post('pass');

        if (isset($log) && isset($pass)){

            foreach($users as $user) {
                if ($user['log'] == $log && $user['pass'] == $pass) {
                    return json_encode($user);
                }
            }
            return json_encode(['error'=>1, 'msg' => 'no such user', 'log' => $log, 'pass' => $pass]);
//            return json_encode(['error'=>1, 'msg' => 'no such user', 'log' => $log, 'pass' => $pass]);

        } else
            return null;

    }

    public function actionDo($id = false){

        $arr = [
            ['id' => 0, 'user' => 'Igor'],
            ['id' => 1, 'user' => 'Ivan'],
            ['id' => 2, 'user' => 'Gosha'],
            ['id' => 3, 'user' => 'Katy'],
        ];


        if ($id != false){
            return json_encode($arr[$id]);
        } else {
            return json_encode(null);
        }



//        var_dump($arr);
//        var_dump($arr);

//        $_COOKIE['name'] = 'Таня';
//
//        $_SESSION[''];
//
//        return json_encode($arr);

    }


}
