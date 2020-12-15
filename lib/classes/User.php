<?php
namespace ArrayIterator;

use ArrayAccess;
use ArrayIterator\Model\AbstractUserModel;
use ArrayIterator\Model\Student;
use ArrayIterator\Model\Supervisor;

/**
 * Class User
 * @package ArrayIterator
 */
class User implements ArrayAccess
{
    protected $user_id;
    protected $site_id;
    protected $uuid;
    protected $type;
    protected $hash;
    protected $hash_type;
    protected $user;

    /**
     * User constructor.
     * @param int $user_id
     * @param int $site_id
     * @param string $uuid
     * @param string $type
     * @param string $hash
     * @param string $hash_type
     * @param AbstractUserModel $user
     */
    public function __construct(
        int $user_id,
        int $site_id,
        string $uuid,
        string $type,
        string $hash,
        string $hash_type,
        AbstractUserModel $user
    ) {
        $this->user_id = $user_id;
        $this->site_id = $site_id;
        $this->uuid = $uuid;
        $this->type = $type;
        $this->hash = $hash;
        $this->hash_type = $hash_type;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return int
     */
    public function getSiteId(): int
    {
        return $this->site_id;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getHashType(): string
    {
        return $this->hash_type;
    }

    /**
     * @return AbstractUserModel|Supervisor|Student
     */
    public function getUser(): AbstractUserModel
    {
        return $this->user;
    }

    public function offsetExists($offset) : bool
    {
        return isset($this->$offset);
    }

    public function offsetGet($offset)
    {
        return $this->$offset??null;
    }

    public function offsetSet($offset, $value)
    {
        // pass
    }

    public function offsetUnset($offset)
    {
        // pass
    }
}
