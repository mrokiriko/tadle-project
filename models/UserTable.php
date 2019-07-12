<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

//class UserTable extends ActiveRecord
class UserTable extends User
{

    public static function tableName(){
        return 'user';
    }

    public function saveUserData(){
        $user = new User();

//        debug($this);
//        $user = Post::model()->findByPk(10);

        $user = UserTable::findOne([ 'id' => Yii::$app->user->identity->getId() ]);

        $user->name = $this->name;
        $user->city = $this->city;
        $user->phone = $this->phone;
        $user->about = $this->about;

        return $user->save();
    }
}