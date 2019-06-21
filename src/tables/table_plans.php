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
 * @property int $sortorder Порядок вывода тарифного плана
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
  * CREATE TABLE `cms_paidaccess_plans` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `groups` TEXT NULL,
    `hint` VARCHAR(500) NOT NULL DEFAULT '',
    `is_active` INT(1) NULL DEFAULT NULL,
    `sortorder` INT(11) NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
    )
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=4
 *
 * @method static row_plan getById($id) Возвращает тариф по ID
 */
class table_plans extends Table {
    protected $_name = 'paidaccess_plans';

    protected $_rowClass = 'row_plan';

    protected $_primary = ['id'];

}