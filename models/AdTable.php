<?php

namespace app\models;

use yii\db\ActiveRecord;

class AdTable extends ActiveRecord {

    public $photo;

    public static function tableName()
    {
        return 'ad';
    }

    public function rules(){
        return [
            [['category', 'price', 'city', 'headline', 'userId'], 'required'],
            [['category', 'city', 'description', 'headline'], 'string'],
            [['price'], 'double'],
            [['date'], 'safe'],
        ];
    }


}