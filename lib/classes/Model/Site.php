<?php

namespace ArrayIterator\Model;

use ArrayIterator\Database\PrepareStatement;

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

        while ($row = $stmt->fetch()) {
            if ($row['type'] === 'host') {
                $stmt->closeCursor();
                return $row;
            }
        }

        $stmt->closeCursor();
        return $row;
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
    public function delete() : bool
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
}
