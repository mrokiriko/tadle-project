<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\AdTable;
use app\models\UserTable;
use app\models\Signup;
use app\models\LoginForm;

class SiteController extends \app\controllers\AppController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionSignup(){
        $model = new Signup();

        if (isset($_POST['Signup'])){
            $model->attributes = Yii::$app->request->post('Signup');
        }

        if ($model->validate() && $model->signup()){
//            return $this->goHome();
            return $this->actionLogin();
        }

        return $this->render('signup', ['model' => $model]);
    }

    public function actionAd()
    {
        $adId = Yii::$app->request->get('id');

//        echo $adId . '+<br>';

        if (isset($adId)){
            $adData = AdTable::find()->where(['id' => $adId])->one();
        }

//            AppController::debug($ads);

        return $this->render('ad', compact('adData'));
    }

    public function actionProfile(){
        $model = new UserTable();

        $loginInfo = Yii::$app->user->identity;

        if (isset($_POST['UserTable'])){

            $model = Yii::$app->request->post('UserTable');

            //var_dump($_POST['UserTable']);
            var_dump($model->name);
            die();


            $model->saveUserData();
        }

        $userId = Yii::$app->request->get('id');
        if (isset($userId)){
            $userData = UserTable::find()->where(['id' => $userId])->one();
        }

        return $this->render('profile', compact(['userData', 'loginInfo', 'model']));
    }

}
