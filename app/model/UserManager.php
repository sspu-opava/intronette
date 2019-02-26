<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;


/**
 * Users management.
 */
final class UserManager implements Nette\Security\IAuthenticator
{
	use Nette\SmartObject;

	const
		TABLE_NAME = 'users',
		COLUMN_ID = 'id',
		COLUMN_NAME = 'username',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_EMAIL = 'email',
		COLUMN_ROLE = 'role',
		COLUMN_FIRSTNAME = 'firstname',
		COLUMN_SURNAME = 'surname',
		COLUMN_STREET = 'street',
		COLUMN_CITY = 'city',
		COLUMN_POSTCODE = 'postcode',
		COLUMN_PHONE = 'phone',
		COLUMN_INFO = 'info',
		COLUMN_PHOTO = 'photo',
		COLUMN_LOGGED = 'logged',
		COLUMN_BANNED = 'banned';


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$row = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_NAME, $username)
			->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!Passwords::verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif (Passwords::needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update([
				self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
			]);
		}

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);
		return new Nette\Security\Identity($row[self::COLUMN_ID], $row[self::COLUMN_ROLE], $arr);
	}

	/**
	 * Get all users.
	 * @return void
	 */
	public function getAll()
	{
		return $this->database->table(self::TABLE_NAME)->fetchAll();
	}

	/**
	 * Get user.
	 * @param  int $id
	 * @return void
	 */
	public function get($id)
	{
		return $this->database->table(self::TABLE_NAME)->get($id);
	}


	/**
	 * Adds new user.
	 * @param  array $values
	 * @return void
	 * @throws DuplicateNameException
	 */
	public function add($values)
	{
		Nette\Utils\Validators::assert($values['email'], 'email');
		$user = [
			self::COLUMN_NAME => $values['username'],
			self::COLUMN_PASSWORD_HASH => Passwords::hash($values['password']),
			self::COLUMN_EMAIL => $values['email'],
			self::COLUMN_FIRSTNAME => $values['firstname'],
			self::COLUMN_SURNAME => $values['surname'],
		];
		try {
			$this->database->table(self::TABLE_NAME)->insert($user);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}

	/**
	 * Edit user profile.
	 * @param  array $values
	 * @return void
	 * @throws Exception
	 */
	public function updateProfile($values)
	{
		Nette\Utils\Validators::assert($values['email'], 'email');
		$user = [
			self::COLUMN_EMAIL => $values['email'],
			self::COLUMN_FIRSTNAME => $values['firstname'],
			self::COLUMN_SURNAME => $values['surname'],
			self::COLUMN_STREET => $values['street'],
			self::COLUMN_CITY => $values['city'],
			self::COLUMN_POSTCODE => $values['postcode'],
			self::COLUMN_PHONE => $values['phone'],
			self::COLUMN_INFO => $values['info'],
		];
		if (isset($values['photo'])) $user[self::COLUMN_PHOTO] = $values['photo'];
		try {
			$this->database->table(self::TABLE_NAME)->get($values['id'])->update($user);
		} catch (Nette\Database\Exception $e) {
			throw new Exception;
		}
	}

	/**
	 * Change user password.
	 * @param  array $values
	 * @return void
	 * @throws Exception
	 */
	public function changePassword($id, $password)
	{
		$user = [
			self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
		];
		try {
			$this->database->table(self::TABLE_NAME)->get($id)->update($user);
		} catch (Nette\Database\Exception $e) {
			throw new Exception;
		}
	}

	/**
	 * Delete user.
	 * @param  array $id
	 * @return void
	 * @throws Exception
	 */
	public function delete($id)
	{
		try {
			return $this->database->table(self::TABLE_NAME)->get($id)->delete();
		} catch (Nette\Database\Exception $e) {
			throw new Exception;
		}
	}

	/**
	 * Banned / active user.
	 * @param  array $id
	 * @return void
	 * @throws Exception
	 */
	public function banned($id)
	{
		try {
			$row = $this->database->table(self::TABLE_NAME)->get($id);
			$user = [self::COLUMN_BANNED => !$row->banned];
			return $this->database->table(self::TABLE_NAME)->get($id)->update($user);
		} catch (Nette\Database\Exception $e) {
			throw new Exception;
		}
	}

}



class DuplicateNameException extends \Exception
{
}
