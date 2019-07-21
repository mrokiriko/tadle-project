<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $name
 * @property string $avatar
 * @property string $city
 * @property string $about
 * @property string $phone
 * @property string $date
 * @property string $auth_key
 * @property string $access_token
 *
 * @property Ad[] $ads
 */
class User extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password', 'date'], 'required'],
//            [['email', 'password', 'name', 'city', 'about', 'phone'], 'string'],
            [['email'], 'email'],
            [['password'], 'string', 'min' => 6, 'max' => 64],
            [['name'], 'string', 'max' => 60],
            [['city'], 'string', 'max' => 30],
            [['about'], 'string', 'max' => 300],
            [['phone'], 'string', 'max' => 10],
//            ['phone', 'match', 'pattern' => '/^(8)[(](\d{3})[)](\d{3})[-](\d{2})[-](\d{2})/', 'message' => 'Телефон должен быть в формате 8(XXX)XXX-XX-XX'],
            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpeg, jpg',
                'maxSize' => 3145728, 'tooBig'=>'Размер файла не должен превышать 3МБ'],
            [['date'], 'safe'],
            [['auth_key', 'access_token'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
            'name' => 'Name',
            'avatar' => 'Avatar',
            'city' => 'City',
            'about' => 'About',
            'phone' => 'Phone',
            'date' => 'Date',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAds()
    {
        return $this->hasMany(AdTable::className(), ['userId' => 'id']);
    }
}
