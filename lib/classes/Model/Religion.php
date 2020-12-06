<?php

namespace ArrayIterator\Model;

use ArrayIterator\Database\PrepareStatement;

/**
 * Class Religion
 * @package ArrayIterator\Meta
 */
class Religion extends Model
{
    protected $tableName = 'sto_religion';

    public function findOneByCode(string $code, int $siteId = null)
    {
        return $this->findOneBy('code', $code, $siteId);
    }

    /**
     * @param string $code
     * @param int|null $siteId
     * @return Religion|false
     */
    public function getByCode(string $code, int $siteId = null)
    {
        $stmt = $this->findOneByCode($code, $siteId);
        if ($res = $stmt->fetch()) {
            $stmt->closeCursor();
        }
        return $res ?: false;
    }

    /**
     * @param string $code
     * @param int|null $siteId
     * @return PrepareStatement|false
     */
    public function findOneByName(string $code, int $siteId = null)
    {
        return $this->findOneBy('name', $code, $siteId);
    }

    /**
     * @param string $name
     * @param int|null $siteId
     * @return Religion|false
     */
    public function getByName(string $name, int $siteId = null)
    {
        $stmt = $this->findOneByName($name, $siteId);
        if ($res = $stmt->fetch()) {
            $stmt->closeCursor();
        }
        return $res ?: false;
    }
}
