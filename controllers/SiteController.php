<?php

namespace app\controllers;

use app\models\PhotoTable;
use SebastianBergmann\Diff\TimeEfficientImplementationTest;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\AdTable;
use app\models\UserTable;
use app\models\Signup;
use app\models\LoginForm;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use app\models\User;

//class SiteController extends \app\controllers\AppController
class SiteController extends Controller
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
        $adModel = new AdTable();
        $adId = Yii::$app->request->get('id');

//        $filename = 'web/uploads/ads/YhSsktoMA2.jpg';
//        $this->actionShowimage($filename);

        if (Yii::$app->request->post('PhotoTable')){
            $picModel = new PhotoTable();
            if ($picModel->saveImg($adId)){
                Yii::$app->session->setFlash('update-success', 'Фото было успешно добавлено');
            } else {
                Yii::$app->session->setFlash('update-failed', 'Произошла ошибка при добавлении фотографии');
            }
        }

        if (Yii::$app->request->post('AdTable')){

            $postData = Yii::$app->request->post('AdTable');
            $updateAdModel = new AdTable();

            $updateAdModel = AdTable::findOne(['id' => $adId]);
            $updateAdModel->attributes = Yii::$app->request->post('AdTable');
            $updateAdModel->status = Yii::$app->request->post('AdTable')['status'];

            if ($updateAdModel->validate()){
                $updateAdModel->save();
                Yii::$app->session->setFlash('update-success', 'Объявление было успено обновлено');
            } else {
                Yii::$app->session->setFlash('update-failed', 'Ошибка при обновлении объявления');
            }
        }

        if (isset($adId)){
            $adData = AdTable::find()->where(['id' => $adId])->one();
            $pictures = PhotoTable::find()->where(['adId' => $adId])->all();
            $user = UserTable::find()->where(['id' => $adData->userId])->one();
        }


        return $this->render('ad', ['adData' => $adData, 'pictures' => $pictures, 'user' => $user, 'adModel' => $adModel]);
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

            $userModel = UserTable::findOne([ 'id' => Yii::$app->user->identity->getId() ]);
            if (isset($userModel->avatar)){
                unlink('uploads/' . $userModel->avatar);
            }

            if ($model->avatar){
                $randString = Yii::$app->security->generateRandomString(10);
                $avatarFile = $loginInfo['id'] . '-avatar-' . $randString . '.jpg';
                $avatarPath = 'uploads/' . $avatarFile;
                $model->avatar->saveAs($avatarPath);
                $model->avatar = $avatarFile;
            }

            $model->saveUserData();
        }

        // Pagination settings
        $userId = Yii::$app->request->get('id');
        if (isset($userId)){
            $userData = UserTable::find()->where(['id' => $userId])->one();
            $userPostsData = AdTable::find()->where(['userId' => $userId]);
            $countPosts = clone $userPostsData;
            $postsPagination = new Pagination(['totalCount' => $countPosts->count()]);
            // default value is 20
            $postsPagination->pageSize = 5;
            $userPosts = $userPostsData->offset($postsPagination->offset)->limit($postsPagination->limit)->all();
        }

        return $this->render('profile', ['userData' => $userData, 'loginInfo' => $loginInfo, 'model' => $model, 'userPosts' => $userPosts, 'postsPagination' => $postsPagination]);
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
                $picModel->saveImg($model->id);
                Yii::$app->session->setFlash('create-success', 'Ваше объявление успешно создано');
            } else {
                Yii::$app->session->setFlash('create-failed', 'Ошибка при создании объявления');
            }
        }

        return $this->render('create', ['model' => $model, 'picModel' => $picModel]);
    }

}
