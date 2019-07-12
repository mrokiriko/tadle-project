<?php

namespace app\models;

use yii\db\ActiveRecord;

class UserTable extends ActiveRecord
{

    public static function tableName(){
        return 'user';
    }

    public function saveUserData(){
        $user = new User();


        $user->name = $this->name;
        $user->city = $this->city;
        $user->phone = $this->phone;
        $user->about = $this->about;

        return $user->save();
    }
}