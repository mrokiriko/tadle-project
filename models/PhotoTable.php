<?php


namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\UploadedFile;
use yii\imagine\Image;
use Imagine\Image\Box;

class PhotoTable extends ActiveRecord
{

    public static function tableName()
    {
        return 'photo';
    }

    public function rules(){
        return [
            [['date', 'adId'], 'required'],
            [['picture'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpeg, jpg',
            'maxSize' => 10485760, 'tooBig'=>'Размер файла не должен превышать 10МБ'],
            [['date'], 'safe'],
        ];
    }



    public function isImgSent(){

        $this->attributes = Yii::$app->request->post('PhotoTable');
        $this->picture = UploadedFile::getInstance($this, 'picture');

        if (isset($this->picture)){
            return true;
        } else {
            return false;
        }

//        debug($this->picture);
//        die();

    }

    public function saveImg($adId){
        $this->attributes = Yii::$app->request->post('PhotoTable');
        $this->picture = UploadedFile::getInstance($this, 'picture');

        $picName = Yii::$app->security->generateRandomString(10) . '.' . $this->picture->getExtension();

        $picFolder = Yii::getAlias('@photo');
        $picPath = $picFolder . '/' . $picName;

        $this->date = date('Y-m-d H:i:s');
        $this->adId = $adId;

        if ($this->picture->saveAs($picPath) &&
            $this->createPreview($picFolder, $picName)){

            $this->picture = $picName;
            $this->save();
            return true;
        } else {
            return false;
        }
    }

    public function createPreview($picFolder, $picName) {

//        $newPicPath = $picPath . '-mini';
//        $this->picture->saveAs($newPicPath);

        try {
            Image::thumbnail($picFolder . '/' . $picName, 300, 300)
                ->resize(new Box(300,300))
                ->save($picFolder . '/m' . $picName,
                    ['quality' => 70]);
        } catch (Exception $err){
            return false;
        }

        return true;

//        unlink('../files/upload/' . $this->pictureFile->baseName . '.'  . $this->pictureFile->extension);
    }

    public function deleteImg(){
        try {
            $src = $this->picture;
            $imgToDelete = $this::findOne(['picture' => $src]);
            $picPath = Yii::getAlias('@photo') . '/' . $src;
            $imgToDelete->delete();

            if (file_exists($picPath))
                unlink($picPath);

            return true;
        } catch (Exception $err){
            return false;
        }

    }

}