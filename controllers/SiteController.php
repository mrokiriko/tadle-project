<?php

namespace app\controllers;

use app\models\PhotoTable;
use Imagine\Image\Box;
use SebastianBergmann\Diff\TimeEfficientImplementationTest;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\AdTable;
use app\models\UserTable;
use app\models\Signup;
use app\models\LoginForm;
use yii\imagine\Image;
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

        $sortFilter = SORT_DESC;
        $categoryFilter = null;
        $cityFilter = null;
        $searchFilter = '';

        $statusFilter = Yii::$app->request->get('status');
        if (!isset($statusFilter))
            $statusFilter = 1;

        $deleteAdId = Yii::$app->request->post('close-ad');
        if (isset($deleteAdId)){
            $deleteAd = AdTable::find()->where(['id' => $deleteAdId])->one();
            if ($deleteAd->userId == Yii::$app->user->getId()){
                $deleteAd->status = 0;
                if ($deleteAd->validate()){
                    $deleteAd->save();
                }
            }
        }

        if (Yii::$app->request->get('sort')){
            $sortFilter = Yii::$app->request->get('sort');
            switch ($sortFilter){
                case 3: $sortFilter = SORT_DESC; break;
                case 4: $sortFilter = SORT_ASC; break;
            }
        }
        if (Yii::$app->request->get('category'))
            $categoryFilter = Yii::$app->request->get('category');

        if (Yii::$app->request->get('city'))
            $cityFilter = Yii::$app->request->get('city');

        if (Yii::$app->request->get('search'))
            $searchFilter = Yii::$app->request->get('search');


        $ads = AdTable::find()->where(['status' => $statusFilter]);

        if ($categoryFilter > 0)
            $ads->andFilterWhere(['category' => $categoryFilter]);

        if ($cityFilter > 0){
            $ads->andFilterWhere(['city' => $cityFilter]);
        }

        $ads = $ads->andFilterWhere(['like', 'LOWER(headline)', strtolower($searchFilter)])->orderBy(['date' => $sortFilter]);



        $countAds = clone $ads;

        $pages = new Pagination(['totalCount' => $countAds->count()]);

        //default value is 20
        $pages->pageSize = 20;

        $models = $ads->offset($pages->offset)->limit($pages->limit)->all();

        foreach ($models as $ad){
            $adPhoto = PhotoTable::find()->where(['adId' => $ad->id])->one();

            if (isset($adPhoto)){
                $ad->photo = $adPhoto;
            }
        }

        return $this->render('index', ['ads' => $ads, 'models' => $models, 'pages' => $pages,
            'sortFilter' => $sortFilter, 'categoryFilter' => $categoryFilter, 'searchFilter' => $searchFilter, 'statusFilter' => $statusFilter, 'cityFilter' => $cityFilter]);
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
            $loginModel = new LoginForm();
            $loginModel->username = $model->email;
            $loginModel->password = $model->password;
            $loginModel->login();
            return $this->goHome();
        }

        return $this->render('signup', ['model' => $model]);
    }

    public function actionAd()
    {
        $adModel = new AdTable();
        $adId = Yii::$app->request->get('id');

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
//            $user->count = count(AdTable::findAll(['userId' => $adData->userId]));
            $userCount = count(AdTable::findAll(['userId' => $adData->userId]));
        }


        return $this->render('ad', ['adData' => $adData, 'pictures' => $pictures, 'user' => $user, 'adModel' => $adModel, 'userCount' => $userCount]);
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
        $miniPicPath = Yii::getAlias('@photo') . '/m' . $picture;

        if(file_exists($picPath))
            unlink($picPath);

        if(file_exists($miniPicPath))
            unlink($miniPicPath);

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
        $this->downloadFile($path);
        return 'really nothing here';
    }

    public function actionProfile(){

        $sortFilter = SORT_DESC;
        $categoryFilter = null;
        $cityFilter = null;
        $searchFilter = '';

        $statusFilter = Yii::$app->request->get('status');

        if (!isset($statusFilter))
            $statusFilter = 1;

        if (Yii::$app->request->get('sort')){
            $sortFilter = Yii::$app->request->get('sort');
            switch ($sortFilter){
                case 3: $sortFilter = SORT_DESC; break;
                case 4: $sortFilter = SORT_ASC; break;
            }
        }
        if (Yii::$app->request->get('category'))
            $categoryFilter = Yii::$app->request->get('category');

        if (Yii::$app->request->get('city'))
            $cityFilter = Yii::$app->request->get('city');

        if (Yii::$app->request->get('search'))
            $searchFilter = Yii::$app->request->get('search');


        $model = new UserTable();

        $loginInfo = Yii::$app->user->identity;

        if (isset($_POST['delete-avatar']) && $_POST['delete-avatar'] != getDefaultAvatar()){

            if(file_exists('uploads/' . $_POST['delete-avatar']))
                unlink('uploads/' . $_POST['delete-avatar']);

            if(file_exists('uploads/m' . $_POST['delete-avatar']))
                unlink('uploads/m' . $_POST['delete-avatar']);

            $model = UserTable::findOne([ 'id' => Yii::$app->user->identity->getId() ]);
            $model->avatar = getDefaultAvatar();

            $model->saveUserData();

//            if ($model->saveUserData()){
//                debug('successful');
//            } else {
//                debug('very bad');
//            }

        } else if (isset($_POST['UserTable'])){
            $model->attributes = Yii::$app->request->post('UserTable');
            $newAvatar = UploadedFile::getInstance($model, 'avatar');

            $userModel = UserTable::findOne([ 'id' => Yii::$app->user->identity->getId() ]);
            $model->avatar = $userModel->avatar;

            if (isset($newAvatar)){
                $model->avatar = $newAvatar;

                if (isset($userModel->avatar) && $userModel->avatar != getDefaultAvatar()){

                    if(file_exists('uploads/' . $userModel->avatar))
                        unlink('uploads/' . $userModel->avatar);

                    if(file_exists('uploads/m' . $userModel->avatar))
                        unlink('uploads/m' . $userModel->avatar);

//                    unlink('uploads/' . $userModel->avatar);
//                    unlink('uploads/m' . $userModel->avatar);
                }

                $randString = Yii::$app->security->generateRandomString(10);
                $avatarFile = $loginInfo['id'] . '-avatar-' . $randString . '.jpg';

                $avatarPath = 'uploads/' . $avatarFile;
                $model->avatar->saveAs($avatarPath);

                $miniAvatarPath = 'uploads/m' . $avatarFile;

                try {
                    Image::thumbnail($avatarPath, 300, 300)
                        ->resize(new Box(300,300))
                        ->save($miniAvatarPath,
                            ['quality' => 70]);
                } catch (Exception $err){
                }


                $model->avatar = $avatarFile;
            }

            $model->saveUserData();
        }

        // Pagination settings
        $userId = Yii::$app->request->get('id');
        if (isset($userId)){
            $userData = UserTable::find()->where(['id' => $userId])->one();

            $userPostsData = AdTable::find()->where(['status' => $statusFilter, 'userId' => $userId]);

            if ($categoryFilter > 0)
                $userPostsData->andFilterWhere(['category' => $categoryFilter]);

            if ($cityFilter > 0){
                $userPostsData->andFilterWhere(['city' => $cityFilter]);
            }

            $userPostsData = $userPostsData->andFilterWhere(['like', 'LOWER(headline)', strtolower($searchFilter)])->orderBy(['date' => $sortFilter]);

            $countPosts = clone $userPostsData;
            $postsPagination = new Pagination(['totalCount' => $countPosts->count()]);
            // default value is 20
            $postsPagination->pageSize = 20;
            $userPosts = $userPostsData->offset($postsPagination->offset)->limit($postsPagination->limit)->all();


            foreach ($userPosts as $ad){
                $adPhoto = PhotoTable::find()->where(['adId' => $ad->id])->one();

                if (isset($adPhoto)){
                    $ad->photo = $adPhoto;
                }
            }

        }

        return $this->render('profile', ['userData' => $userData, 'loginInfo' => $loginInfo, 'model' => $model, 'userPosts' => $userPosts, 'postsPagination' => $postsPagination,
            'sortFilter' => $sortFilter, 'categoryFilter' => $categoryFilter, 'searchFilter' => $searchFilter, 'statusFilter' => $statusFilter, 'cityFilter' => $cityFilter]);


    }

    public function actionCreate(){
        $model = new AdTable();
        $picModel = new PhotoTable();

        if (Yii::$app->request->post('AdTable')){
            $model->attributes = Yii::$app->request->post('AdTable');
            $model->userId = Yii::$app->user->getId();

            if ($model->validate()){
                $model->status = true;
                $model->date = date('Y-m-d G:i:s');
                $model->save();

                $picModel->attributes = Yii::$app->request->post('PhotoTable');

                if ($picModel->isImgSent()){
                    $picModel->saveImg($model->id);
                }

                Yii::$app->session->setFlash('create-success', 'Ваше объявление успешно создано');
            } else {
                Yii::$app->session->setFlash('create-failed', 'Ошибка при создании объявления');
            }
        }

        return $this->render('create', ['model' => $model, 'picModel' => $picModel]);
    }

}
