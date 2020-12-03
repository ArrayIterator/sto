<?php
namespace ArrayIterator\Model;

/**
 * Class Student
 * @package ArrayIterator\Model
 */
class Student extends AbstractUserModel
{
    protected $tableName = 'sto_student';
    protected $primaryKey = 'id';
}
