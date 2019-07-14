<?php
namespace pdima88\icms2paidaccess\tables;

use pdima88\icms2ext\Table;
use cmsModel;
use Zend_Db_Table_Row_Abstract;

/**
 * Тариф
 * @property int $id ID
 * @property string $title Название тарифного плана
 * @property array $groups Группы, присваиваемые пользователю
 * @property string $hint Описание тарифного плана
 * @property bool $is_active Активен ли тарифный план
 * @property int $sortorder Порядок вывода тарифного плана
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

    function getByUserId($userId) {
        return $this->fetchRow(['user_id = ?' => $userId]);
    }
}