<?php

namespace app\controllers;

use app\models\PhotoTable;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\AdTable;
use app\models\UserTable;
use app\models\Signup;
use app\models\LoginForm;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use app\models\User;

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
        $ads = AdTable::find()->where(['status' => 1]);


        $countAds = clone $ads;

        $pages = new Pagination(['totalCount' => $countAds->count()]);

        //default value is 20
        $pages->pageSize = 5;

        $models = $ads->offset($pages->offset)->limit($pages->limit)->all();

        foreach ($models as $ad){

            $adPhoto = PhotoTable::find()->where(['adId' => $ad->id])->one();

            if (isset($adPhoto)){
                $ad->photo = $adPhoto;
            }
        }

        return $this->render('index', ['ads' => $ads, 'models' => $models, 'pages' => $pages]);
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
//            debug($_POST['Signup']);
//            die();
        }

        if ($model->validate() && $model->signup()){
            return $this->goHome();
//            $model->delete();
//            unset($_POST['Signup']);
//            return $this->actionLogin();
        }

        return $this->render('signup', ['model' => $model]);
    }

    public function actionAd()
    {
        $adId = Yii::$app->request->get('id');

//        $filename = 'web/uploads/ads/YhSsktoMA2.jpg';
//        $this->actionShowimage($filename);


        if (isset($adId)){
            $adData = AdTable::find()->where(['id' => $adId])->one();
            $pictures = PhotoTable::find()->where(['adId' => $adId])->all();
            $user = UserTable::find()->where(['id' => $adData->userId])->one();
        }

//            AppController::debug($ads);

        return $this->render('ad', compact(['adData', 'pictures', 'user']));
    }

    public function downloadFile($fullpath){
        if(!empty($fullpath)){
            header("Content-type:application/pdf"); //for pdf file
            //header('Content-Type:text/plain; charset=ISO-8859-15');
            //if you want to read text file using text/plain header
            header('Content-Disposition: attachment; filename="'.basename($fullpath).'"');
            header('Content-Length: ' . filesize($fullpath));
            readfile($fullpath);
            Yii::$app->end();

        }
    }

    function actionShowimage($filename = false) {

        $path = Yii::$app->basePath . '/web/' . Yii::getAlias('@photo') . '/' . $filename;

//        debug($path);
//        die();

        $this->downloadFile($path);

        return 'really nothing here';
    }

    public function actionProfile(){
        $model = new UserTable();

        $loginInfo = Yii::$app->user->identity;

        if (isset($_POST['UserTable'])){


            $model->attributes = Yii::$app->request->post('UserTable');
            $model->avatar = UploadedFile::getInstance($model, 'avatar');

//            debug($model->avatar);
//            die();

            if ($model->avatar){
//                BaseFileHelper::createDirectory('uploads/');
//                $randString = implode(array_slice(array_merge(range(0,9), range('a','z'), range('A','Z')), 0, 10));
                $randString = Yii::$app->security->generateRandomString(10);
                $avatarFile = $loginInfo['id'] . '-avatar-' . $randString . '.jpg';
                $avatarPath = 'uploads/' . $avatarFile;
                $model->avatar->saveAs($avatarPath);
                $model->avatar = $avatarFile;
            }
            $model->saveUserData();
        }

        $userId = Yii::$app->request->get('id');
        if (isset($userId)){
            $userData = UserTable::find()->where(['id' => $userId])->one();
        }

        return $this->render('profile', compact(['userData', 'loginInfo', 'model']));
    }

    public function actionCreate(){

        $model = new AdTable();

        $picModel = new PhotoTable();

        if (Yii::$app->request->post('AdTable') && Yii::$app->request->post('PhotoTable')){
            $model->attributes = Yii::$app->request->post('AdTable');
            $model->userId = Yii::$app->user->getId();

            if ($model->validate()){
                $model->status = true;
                $model->date = date('Y-m-d H:i:s');
                $model->save();
                Yii::$app->session->setFlash('create-success', 'Ваше объявление успешно создано');
            } else {
                Yii::$app->session->setFlash('create-failed', 'Ошибка при создании объявления');
            }

            $picModel->attributes = Yii::$app->request->post('PhotoTable');

            $picModel->picture = UploadedFile::getInstance($picModel, 'picture');

            $picName = Yii::$app->security->generateRandomString(10) . '.' . $picModel->picture->getExtension();

//            BaseFileHelper::createDirectory('uploads/ads/');
//            $picPath = 'uploads/ads/' . $picName;

            $picPath = Yii::getAlias('@photo') . '/' . $picName;
//            $picPath = $picName;

            $picModel->date = date('Y-m-d H:i:s');
            $picModel->adId = $model->id;

//            debug($picPath);
//            die();

            $picModel->picture->saveAs($picPath);
            $picModel->picture = $picName;

            $picModel->save();
        }

        return $this->render('create', ['model' => $model, 'picModel' => $picModel]);
    }



}
