<?php

declare(strict_types = 1);

namespace App\Model;

use Nette;

final class ZooManager {

    use Nette\SmartObject;

    private const
		TABLE_NAME = 'event',
		COLUMN_ID = 'id',
		COLUMN_DATE = 'date',
		COLUMN_TIME = 'time',
		COLUMN_SUMMARY = 'summary',
		COLUMN_DESCRIPTION = 'description',
                COLUMN_VISITORS = 'visitors',
                COLUMN_TYPE = 'type';

    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }
    
    public function getAll($order = self::COLUMN_DATE) {
        return $this->database->table(self::TABLE_NAME)->order($order)->fetchAll();
    }

    public function getById($id) {
        return $this->database->table(self::TABLE_NAME)->get($id);
    }
    
    public function insert($values) {
        //try 
        //{
            $this->database->table(self::TABLE_NAME)->insert($values);
            return true;
        //} 
		//catch (Nette\Database\DriverException $e) 
		//{
		//	return false;
		//}	
    }

    public function update($id, $values) {
        if ($zaznam = $this->getById($id)) return $zaznam->update($values);
        return false;
    }

    public function delete($id) {
        if ($zaznam = $this->getById($id)) return $zaznam->delete();
        return false;
    }
}
