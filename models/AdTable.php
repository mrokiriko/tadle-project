<?php

namespace app\models;

use yii\db\ActiveRecord;

class AdTable extends ActiveRecord {

    public static function tableName()
    {
        return 'ad';
    }

}