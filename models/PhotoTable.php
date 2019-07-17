<?php


namespace app\models;

use yii\db\ActiveRecord;

class PhotoTable extends ActiveRecord
{

    public static function tableName()
    {
        return 'photo';
    }

    public function rules(){
        return [
            [['picture', 'date', 'adId'], 'required'],
            [['picture'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpeg, jpg'],
            [['date'], 'safe'],
        ];
    }


}