<?php
namespace pdima88\icms2paidaccess\tables;

use pdima88\icms2ext\Table;
use cmsModel;
use Zend_Db_Table_Row_Abstract;

/**
 * Тариф
 * @property int $id ID тарифного плана
 * @property string $title Название тарифного плана
 * @property array $groups Группы, присваиваемые пользователю
 * @property string $hint Описание тарифного плана
 * @property bool $is_active Активен ли тарифный план
 * @property int $level Уровень доступа
 */
class row_plan extends Zend_Db_Table_Row_Abstract {
    function __get($columnName)
    {
        if ($columnName == 'groups') {
            return cmsModel::yamlToArray($this->_data['groups']);
        }
        return parent::__get($columnName);
    }
}

/**
 * @method static row_plan getById($id) Возвращает тариф по ID
 */
class table_plans extends Table {
    protected $_name = 'paidaccess_plans';

    protected $_rowClass = __NAMESPACE__.'\\row_plan';

    protected $_primary = ['id'];

    /**
     * Возвращает минимальный тарифный план удовлетворяющий указанному уровню доступа
     *
     * @param int Минимальный уровень доступа
     * @return row_plan
     */
    function getByMinLevel($level) {
        return $this->fetchRow(['level >= ? AND is_active = 1' => $level], 'level ASC');
    }

        /**
     * Возвращает тарифный план с указанным уровнем доступа
     *
     * @param int уровень доступа, число от 1 до N
     * @return row_plan
     */
    function getByLevel($level) {
        return $this->fetchRow(['level = ?' => $level], 'is_active DESC');
    }
}