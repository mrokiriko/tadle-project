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
//            [['category', 'city', 'description', 'headline'], 'string'],
            [['category'], 'string', 'max' => 2],
            [['city'], 'string', 'max' => 30],
            [['headline'], 'string', 'max' => 40],
            [['description'], 'string', 'max' => 1000],
//            [['price'], 'double'],
            [['price'], 'integer', 'min' => 1, 'max' => 1000000000],
            [['date'], 'safe'],
        ];
    }


}