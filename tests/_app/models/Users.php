<?php
declare(strict_types = 1);

namespace app\models;

use pozitronik\helpers\Utils;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "sys_users".
 *
 * @property int $id
 * @property string $username Отображаемое имя пользователя
 * @property string $login Логин
 * @property string $password Хеш пароля либо сам пароль (если $salt пустой)
 * @property-read string $authKey @see [[yii\web\IdentityInterface::getAuthKey()]]
 */
class Users extends ActiveRecord implements IdentityInterface {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName():string {
		return 'users';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules():array {
		return [
			[['username', 'login', 'password'], 'string'],
			[['username', 'login', 'password'], 'required']
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels():array {
		return [
			'id' => 'ID',
			'username' => 'Имя пользователя',
			'login' => 'Логин',
			'password' => 'Пароль',
		];
	}

	/**
	 * @inheritDoc
	 */
	public static function findIdentity($id) {
		return static::findOne($id);
	}

	/**
	 * @inheritDoc
	 */
	public static function findIdentityByAccessToken($token, $type = null):?IdentityInterface {
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @inheritDoc
	 */
	public function getAuthKey():string {
		return md5($this->id.md5($this->login));
	}

	/**
	 * @inheritDoc
	 */
	public function validateAuthKey($authKey):bool {
		return $this->authKey === $authKey;
	}

	/**
	 * Создать пользователя
	 * @return static
	 */
	public static function CreateUser():self {
		return new self([
			'id' => 1,
			'login' => 'test',
			'username' => 'test_user',
			'password' => 'test',
		]);
	}

	/**
	 * @return static
	 * @throws Exception
	 */
	public function saveAndReturn():static {
		if (!$this->save()) {
			throw new Exception(sprintf("Не получилось сохранить запись: %s", Utils::Errors2String($this->firstErrors)));
		}
		$this->refresh();
		return $this;
	}
}
