<?php

namespace ArrayIterator\Model;

use ArrayIterator\Database\PrepareStatement;
use PDOStatement;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class AbstractUserModel
 * @package ArrayIterator\Model
 * @property int|null $id
 * @property string|null $username
 * @property string|null $email
 * @property string|null $password
 * @method int|null getUsername()
 */
abstract class AbstractUserModel extends Model
{
    abstract public function getUserRoleType(): string;

    /**
     * @param string $password
     * @return string
     */
    public function encodePlainPassword(string $password): string
    {
        return sha1($password);
    }

    /**
     * Check if is Password hashed with sha1
     * @param string $password
     * @return bool
     */
    public function isPasswordEncoded(string $password): bool
    {
        return (bool)preg_match('~^[a-f0-9]{40}$~', $password);
    }

    public function hasPassword(string $password, $reEncode = true)
    {
        return password_hash(
            $reEncode ? $this->encodePlainPassword($password) : $password,
            PASSWORD_BCRYPT
        );
    }

    /**
     * @param string $pass
     * @return bool
     */
    public function isPasswordMatch(string $pass): bool
    {
        $password = $this->getPassword();
        if (!is_string($password)) {
            return false;
        }
        $pass = $this->encodePlainPassword($pass);
        if (password_needs_rehash($password, PASSWORD_BCRYPT)) {
            $password = $this->isPasswordEncoded($password)
                ? $password
                : $this->encodePlainPassword($password);
            return $pass === $password;
        }

        return password_verify($pass, $password);
    }

    /**
     * @return string|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    private function getPassword()
    {
        return $this->data['password'] ?? null;
    }

    public function setUsername(string $username)
    {
        $username = strtolower(trim($username));
        if (!$username || preg_match('~[^a-z0-9_.\-]~', $username)) {
            return false;
        }

        if (array_key_exists('username', $this->data) && $this->data['username'] === $username) {
            return $username;
        }
        return $this->userData['username'] = $username;
    }

    /**
     * @param string $email
     * @return false|string
     */
    public function setEmail(string $email)
    {
        $email = strtolower(trim($email));
        if (!$email || !($email = filter_var($email, FILTER_VALIDATE_EMAIL))) {
            return false;
        }
        if (array_key_exists('email', $this->data) && $this->data['email'] === $email) {
            return $this->data['email'];
        }

        return $this->userData['email'] = $email;
    }

    /**
     * @param string $password
     * @return false|string|null
     */
    public function setPassword(string $password)
    {
        $this->userData['password'] = $this->hasPassword($password);
        return $this->userData['password'];
    }

    /**
     * @param string $gender
     * @return string
     */
    public function setGender(string $gender): string
    {
        $gender = strtoupper($gender);
        if (array_key_exists('gender', $this->data) && $this->data['gender'] === $gender
            && in_array($gender, ['F', 'M'])
        ) {
            return $gender;
        }

        return $this->userData['gender'] = strpos($gender, 'F') !== false
            ? 'F'
            : (strpos($gender, 'P') !== false ? 'F' : 'M');
    }

    public function __destruct()
    {
        if ($this->fromStatement && $this->database) {
            $password = $this->data['password'] ?? null;
            $id = $this->getId();
            if (!$id
                || $id < 1
                || !is_string($password)
                || !password_needs_rehash($password, PASSWORD_BCRYPT)
            ) {
                return;
            }

            $password = $this
                ->hasPassword(
                    $password,
                    !$this->isPasswordEncoded($password)
                );
            $this->data['password'] = $password;
            $stmt = $this
                ->prepare(
                    sprintf('UPDATE %s SET password=? WHERE id=?', $this->getTableName())
                );
            $stmt->execute([$password, $id]);
            $stmt->closeCursor();
        }
    }

    public function findOneById(int $id)
    {
        return $this->findOne($id, 'id');
    }

    /**
     * @param $id
     * @return bool|AbstractUserModel
     */
    public function getById(int $id)
    {
        $stmt = $this->findOneById($id);
        if ($res = $stmt->fetch()) {
            $stmt->closeCursor();
        }

        return $res ?: false;
    }

    public function findById(int $id)
    {
        return $this->findOneById($id);
    }

    public function findOneByUsername(string $username, int $siteId = null)
    {
        return $this->findOne($username, 'username', $siteId);
    }

    public function findOneByEmail(string $username, int $siteId = null)
    {
        return $this->findOne($username, 'email', $siteId);
    }

    /**
     * @return string
     */
    abstract public function objectUserLogClassName() : string;

    /**
     * @return AbstractUserLog|StudentLogs|SupervisorLogs
     */
    public function getObjectUserLog() : AbstractUserLog
    {
        $obj = $this->objectUserLogClassName();
        return new $obj($this->database);
    }
}
