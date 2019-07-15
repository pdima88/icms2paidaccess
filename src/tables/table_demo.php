<?php
namespace pdima88\icms2paidaccess\tables;

use pdima88\icms2ext\Table;
use cmsModel;
use Zend_Db_Table_Row_Abstract;

/**
 * Тариф
 * @property int $id ID
 * @property int $user_id ID пользователя
 * @property string $when_activated Дата активации демо-доступа
 * @property string $when_expiried Дата истечения срока демо-доступа, если NULL - бессрочно
 */
class row_demo extends Zend_Db_Table_Row_Abstract {
    function __get($columnName)
    {
        if ($columnName == 'user') {
            return $this->findParentRow('tableUsers');
        }
        return parent::__get($columnName);
    }
}

/**
 * @method static row_demo getById($id) Возвращает запись демо-доступа по ID
 */
class table_demo extends Table {
    protected $_name = 'paidaccess_demo';

    protected $_rowClass = __NAMESPACE__.'\\row_demo';

    protected $_primary = ['id'];

    protected $_referenceMap = [
        'User' => [
            self::COLUMNS           => 'user_id',
            self::REF_TABLE_CLASS   => '\\tableUsers',
            self::REF_COLUMNS       => 'id'
        ],
    ];

    const FK_USER = __CLASS__.'.User';

    /**
     * @param $userId
     * @return null|row_demo
     */
    function getByUserId($userId) {
        return $this->fetchRow(['user_id = ?' => $userId]);
    }
}