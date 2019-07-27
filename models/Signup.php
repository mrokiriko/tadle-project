<?php


namespace app\models;

use yii\base\Model;

class Signup extends Model
{
    public $email;
    public $password;

    public function rules(){

        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass'=>'app\models\User'],
            ['password', 'string', 'min' => 6, 'max' => 64],
        ];
    }


    public function signup(){
        $user = new User();
        $user->email = $this->email;
        $user->password = md5($this->password);
        $user->date = date('Y-m-d G:i:s');
        $user->avatar = getDefaultAvatar();

        return $user->save();
    }

}