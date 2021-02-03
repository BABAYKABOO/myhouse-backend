<?php
namespace backend\models;

use common\models\Profile;
use common\models\User;
use common\models\UserNote;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * User form
 */
class UserForm extends Model
{
    public $firstname;
    public $lastname;
    public $middlename;
    public $birthdate;
    public $email;
    public $phone;
    public $viber;
    public $telegram;
    public $status;
    public $uid;
    public $note;
    public $password;
    public $password2;
    
    public $image;

    private $_model = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'middlename', 'birthdate', 'email', 'phone', 'viber', 'telegram', 'uid'], 'string', 'max' => 255],
            [['password', 'password2'], 'string', 'min' => 6, 'max' => 255],
            ['email', 'email'],
            ['email', 'required'],
            ['note', 'string'],
            [
                'email',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => 'Этот логин уже используется.',
                'when' => function ($model) {
                    $user = User::findByUsername($model->email);
                    return $user && $user->id != Yii::$app->request->get('id');
                }
            ],
            [
                'uid',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => 'Этот ID уже используется.',
                'when' => function ($model) {
                    $user = User::findByUsername($model->uid);
                    return $user && $user->id != Yii::$app->request->get('id');
                }
            ],
            ['password', 'required', 'when' => function ($model) {
                $user = User::findByUsername($model->email);
                return !$user;
            }, 'whenClient' => "function (attribute, value) {
                return $('#is_new_record').val() == '1';
            }", 'skipOnEmpty' => true],
            ['password2', 'required', 'when' => function ($model) {
                return $model->password != '';
            }, 'whenClient' => "function (attribute, value) {
                return $('#userform-password').val() != '';
            }"],
            ['password2', 'compare', 'compareAttribute' => 'password', 'message'=> Yii::t('model', 'Пароли не совпадают.')],
            ['status', 'default', 'value' => User::STATUS_NEW],
            ['status', 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_NEW, User::STATUS_DELETED]],
            ['image', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('model', 'Email (логин)'),
            'firstname' => Yii::t('model', 'Имя'),
            'lastname' => Yii::t('model', 'Фамилия'),
            'middlename' => Yii::t('model', 'Отчество'),
            'birthdate' => Yii::t('model', 'Дата рождения'),
            'phone' => Yii::t('model', 'Телефон'),
            'viber' => Yii::t('model', 'Viber'),
            'telegram' => Yii::t('model', 'Telegram'),
            'uid' => Yii::t('model', 'ID'),
            'note' => Yii::t('model', 'О владельце (заметки)'),
            'status' => Yii::t('model', 'Статус'),
            'password' => Yii::t('model', 'Пароль'),
            'password2' => Yii::t('model', 'Повторить пароль'),
            'image' => Yii::t('model', 'Сменить изображение'),
        ];
    }

    /**
     * Save user.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function process()
    {
        if ($this->validate()) {
            $user = User::find()->where(['or', ['email' => $this->_model->email], ['uid' => $this->_model->uid]])->one();
            if (!$user) {
                $user = new User();
            }
            $user->email = $this->email;
            $user->uid = $this->uid;
            if ($this->password) {
                $user->setPassword($this->password);

                // send sms
//                $phonesms = $user->username;
//                $message = 'Новый пароль: ' . $this->password;
//                $tableName = 'avadav';
//                $sign = 'Msg';
//                $dbSms = Yii::$app->dbSms;
//                if (!YII_DEBUG) {
//                    $dbSms->createCommand()->insert($tableName, [
//                        'number' => $phonesms,
//                        'sign' => $sign,
//                        'message' => $message,
//                    ])->execute();
//                }
//                $res = " $phonesms ";
//                file_put_contents(Yii::getAlias('@app').'/web/smslog.txt', $res . ' @@ ' . $message . "\n", FILE_APPEND);
            }
            if ($user->isNewRecord) {
                $user->generateAuthKey();
            }
            $user->status = $this->status;

            if ($user->save()) {
                // uid
                if (!$user->uid) {
                    $user->uid = sprintf('%06d', $user->id);
                    $user->save();
                }
                
                $profile = $user->profile ? $user->profile : new Profile();
                if ($profile->isNewRecord) {
                    $profile->user_id = $user->id;
                }
                $profile->firstname = $this->firstname;
                $profile->lastname = $this->lastname;
                $profile->middlename = $this->middlename;
                $profile->phone = $this->phone;
                $profile->viber = $this->viber;
                $profile->telegram = $this->telegram;
                $profile->birthdate = date('Y-m-d', strtotime($this->birthdate));
                $profile->save();
                // image
                $file = UploadedFile::getInstance($this, 'image');
                if ($file) {
                    $path = '/upload/User/' . $user->id . '/avatar.' . $file->extension; 
                    $pathFull = Yii::getAlias('@frontend/web' . $path);
                    $dir = dirname($pathFull);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    if ($file->saveAs($pathFull)) {
                        $profile->image = $path;
                        $profile->save(false);
                        Yii::$app->glide->getServer()->deleteCache($path);
                    }
                }
                
                $note = $user->userNote ? $user->userNote : new UserNote();
                if ($note->isNewRecord) {
                    $note->user_id = $user->id;
                }
                $note->description = $this->note;
                $note->save();
                
                if ($this->password) {
                    // send email new password
                    $this->sendNewPassword($user);
                }
                
                $this->_model = $user;
                
                return $user;
            }
        }

        return null;
    }

    /**
     * @param User $model
     * @return UserForm
     */
    public static function loadFromModel($model) {
        $form = new static();
        $form->_model = $model;
        if ($model) {
            $form->email = $model->email;
            $form->firstname = $model->profile->firstname;
            $form->lastname = $model->profile->lastname;
            $form->middlename = $model->profile->middlename;
            $form->phone = $model->profile->phone;
            $form->viber = $model->profile->viber;
            $form->telegram = $model->profile->telegram;
            $form->birthdate = $model->profile->birthdate ? date(Yii::$app->params['dateFormat'], strtotime($model->profile->birthdate)) : '';
            $form->status = $model->status;
            $form->uid = $model->uid;
            $form->note = $model->userNote->description;
        }
        return $form;
    }
    
    /**
     * 
     * @return type
     */
    public function getAvatar()
    {
        if (!$this->_model || !$this->_model->profile) {
            return Yii::getAlias('/upload/placeholder.jpg');
        }
        return $this->_model->profile->getAvatar();
    }
    
    /**
     * 
     * @param User $user
     */
    private function sendNewPassword($user)
    {
        $urlCabinet = Yii::$app->urlManagerCabinet->createAbsoluteUrl(['/']);
        $email = $user->email;
        $title = 'Пароль для входа';
        $message = 'Ваш пароль для входа в кабинет "' . Yii::$app->name . '":'
            . "\r\n" . $this->password
            . "\r\n \r\n" . 'Ссылка для входа: <a href="' . $urlCabinet . '">' . $urlCabinet . '</a>';
        
        \Yii::$app->mailer->compose()
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
            ->setTo($email)
            ->setSubject($title)
            ->setTextBody(strip_tags($message))
            ->setHtmlBody(nl2br($message))
            ->send();
    }
}
