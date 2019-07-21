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


    public function actionDeleteimage($picture){

        $imgToDelete = PhotoTable::findOne(['picture' => $picture]);
        $picPath = Yii::getAlias('@photo') . '/' . $picture;
        unlink($picPath);
        $imgToDelete->delete();
        if (Yii::$app->request->isAjax)
        {
            return true;
        } else {
            return $this->redirect(['site/index']);
        }

    }

    public function actionAjax(){

        $photoModel = new PhotoTable();
        $err_msg = '';
        $adId = 5;


        if (Yii::$app->request->post() && $photoModel->saveImg($adId)){
            $err_msg = 'so very cool';
        } else {
            $err_msg = 'i fucked somethign up';
        }

        $pictures = PhotoTable::find()->where(['adId' => $adId])->all();

        return $this->render('ajax',
            [
                'error' => $err_msg,
                'photoModel' => $photoModel,
                'pictures' => $pictures,
            ]);
    }

}
