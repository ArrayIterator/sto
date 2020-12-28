<?php

namespace ArrayIterator\Model;

use ArrayIterator\Database\PrepareStatement;

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

/**
 * Class Site
 * @package ArrayIterator\Model
 */
class Site extends Model
{
    protected $tableName = 'sto_sites';

    /**
     * @var string[]
     */
    protected $primaryKey = [
        'id',
        'host',
        'additional_host',
        'token',
    ];

    private static $static_host = null;
    private static $static_additional_host = null;

    /**
     * @return null
     */
    public function getModelSiteId()
    {
        return null;
    }

    public function findOneById(int $id)
    {
        return $this->findOne($id, 'id');
    }

    /**
     * @param int $id
     * @return Site|false
     */
    public function getById(int $id)
    {
        $stmt = $this->findOneById($id);
        $res = $stmt->fetch();
        $stmt && $stmt->closeCursor();
        return $res ?: false;
    }

    public function findById(int $id)
    {
        return $this->findOneById($id);
    }

    /**
     * @return PrepareStatement|bool
     */
    public function getAllStmt()
    {
        $stmt = $this->unbufferedPrepare(
            sprintf(
                "SELECT * FROM %s",
                $this->getTableName()
            )
        );
        if (!$stmt->execute()) {
            return false;
        }
        return $stmt;
    }

    /**
     * @param string $host host could not be empty
     * @return false|PrepareStatement
     */
    public function findOneByHost(string $host)
    {
        $host = trim(strtolower($host));
        if ($host === '') {
            return false;
        }
        $stmt = $this->prepare(
            sprintf('SELECT * FROM %s WHERE LOWER(TRIM(host))=?', $this->getTableName())
        );

        if ($stmt->execute([$host])) {
            return $stmt;
        }
        return false;
    }

    /**
     * @return int|null
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getSiteId()
    {
        $id = $this->data['id'] ?? null;
        return is_numeric($id) ? abs(intval($id)) : null;
    }

    /**
     * @param string $host
     * @return Site|false
     */
    public function getByHost(string $host)
    {
        $stmt = $this->findOneByHost($host);
        $res = $stmt->fetch();
        $stmt && $stmt->closeCursor();
        return $res ?: false;
    }

    /**
     * @param string $host host could not be empty
     * @return false|PrepareStatement
     */
    public function findOneAdditionalHost(string $host)
    {
        $host = trim(strtolower($host));
        if ($host === '') {
            return false;
        }
        $stmt = $this->prepare(
            sprintf('SELECT * FROM %s WHERE LOWER(TRIM(additional_host))=?', $this->getTableName())
        );
        if ($stmt->execute([$host])) {
            return $stmt;
        }
        return false;
    }

    /**
     * @param string $host
     * @return Site|false
     */
    public function getByAdditionalHost(string $host)
    {
        $stmt = $this->findOneByHost($host);
        $res = $stmt->fetch();
        $stmt && $stmt->closeCursor();
        return $res ?: false;
    }

    /**
     * @param string $host
     * @return array|Model|false|mixed|object
     */
    public function getHostOrAdditionalMatch(string $host)
    {
        $host = trim(strtolower($host));
        if ($host === '') {
            return false;
        }
        $sql = sprintf(
            "SELECT *,
            (
                case 
                WHEN LOWER(TRIM(host))=:h then 'host'
                WHEN LOWER(TRIM(additional_host))=:h then 'additional_host'
                end
            ) as type FROM %s
             WHERE LOWER(trim(host))=:h OR LOWER(trim(additional_host))=:h
                LIMIT 2

             ",
            $this->getTableName()
        );

        $stmt = $this->prepare($sql);
        if (!$stmt->execute([':h' => $host])) {
            return false;
        }
        $lastRowFound = false;
        $foundRow = false;
        while ($row = $stmt->fetch()) {
            if ($row['type'] === 'host') {
                $lastRowFound = $row;
                $foundRow = $row;
                break;
            }

            if ($row['type'] === 'additional_host') {
                $lastRowFound = $row;
            }
        }

        if ($foundRow) {
            $lastRowFound = $foundRow;
        }
        if ($lastRowFound && isset($lastRowFound['id'])) {
            $lastRowFound['id'] = abs(intval($lastRowFound['id']));
        }

        $stmt->closeCursor();
        return $lastRowFound;
    }

    /**
     * @param array $where
     * @return array|Model|bool|int|mixed|object
     */
    public function save(array $where = [])
    {
        $id = $this->userData['id'] ?? null;
        if ($id === null) {
            $id = $where['id'] ?? null;
        }
        if ($id !== null && (!is_numeric($id) || abs($id) !== 1)) {
            return false;
        }
        $id = $id !== null ? abs($id) : null;
        if ($id === 1 || (abs($this->data['id'] ?? -3) === 1)) {
            if (isset($this->userData['status']) && $this->userData['status'] !== 'active') {
                $this->userData['status'] = 'active';
            }
        }

        return parent::save($where);
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        // disallow delete site id =1
        $id = $this->userData['id'] ?? null;
        if ($id !== null && (!is_numeric($id) || abs($id) !== 1)) {
            return false;
        }

        $id = $id !== null ? abs($id) : null;
        if ($id === 1 || (abs($this->data['id'] ?? -3) === 1)) {
            return false;
        }

        return parent::delete();
    }

    public function __set($name, $value)
    {
        parent::__set($name, $value);
        if ($this->isFromStatement()
            && isset($this->data['id'])
        ) {
            $site_id = $this->data['id'];
            if (!is_int($site_id)) {
                return;
            }

            if ($name === 'id') {
                $this->setModelSiteId($site_id);
                return;
            }

            if ($site_id === 1) {
                $this->data['status'] = 'active';
                if (self::$static_host === null) {
                    self::$static_host = defined('DEFAULT_SITE_HOST')
                        && is_string(DEFAULT_SITE_HOST)
                        && trim(DEFAULT_SITE_HOST) !== ''
                        ? DEFAULT_SITE_HOST
                        : false;
                }

                if (self::$static_additional_host === null) {
                    self::$static_additional_host = defined('DEFAULT_ADDITIONAL_HOST')
                    && is_string(DEFAULT_ADDITIONAL_HOST)
                    && trim(DEFAULT_ADDITIONAL_HOST) !== ''
                        ? DEFAULT_ADDITIONAL_HOST
                        : false;
                }

                if (self::$static_host
                    && array_key_exists('host', $this->data)
                    && (!$this->data['host']
                        || !is_string($this->data['host'])
                        || trim($this->data['host']) === ''
                    )
                ) {
                    $this->data['host'] = self::$static_host;
                }

                if (self::$static_additional_host
                    && array_key_exists('additional_host', $this->data)
                    && (
                        ! $this->data['additional_host']
                        || !is_string($this->data['additional_host'])
                        || trim($this->data['additional_host']) === ''
                    )
                ) {
                    $this->data['additional_host'] = self::$static_additional_host;
                }
            }
        }
    }
}
