<?php
namespace pdima88\icms2paidaccess\tables;

use pdima88\icms2ext\Format;
use pdima88\icms2ext\Table;
use Zend_Db_Table_Row_Abstract;

/**
 * Тариф
 * @property int $id ID тарифа
 * @property int $plan_id ID тарифного плана
 * @property row_plan $plan Тарифный план
 * @property string $name Автоматически формируемое название (из срока действия тарифа, например, 1 месяц, 3 дня и т.д.)
 * @property int $period Срок действия тарифа (дней)
 * @property float $price Цена
 * @property int $bonus Количество вопросов
 * @property bool $is_active Активен ли тариф
 */
class row_tariff extends Zend_Db_Table_Row_Abstract {
    function __get($columnName)
    {
        if ($columnName == 'plan') {
            return $this->findParentRow(__NAMESPACE__.'\\table_plans');
        }
        if ($columnName == 'name') {
            return Format::formatDuration($this->period);
        }
        return parent::__get($columnName);
    }

    function toArray()
    {
        $res = parent::toArray();
        $res['name'] = $this->name;
        return $res;
    }
}

/**
  * CREATE TABLE `cms_paidaccess_tariffs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `plan_id` INT(11) NOT NULL,
    `period` INT(11) NOT NULL,
    `price` FLOAT NOT NULL,
    `bonus` INT(11) NOT NULL,
    `is_active` INT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
    )
    COLLATE='utf8_general_ci'
    ENGINE=MyISAM
    AUTO_INCREMENT=28
 *
 * @method static row_tariff getById($id) Возвращает тариф по ID
 */
class table_tariffs extends Table {
    protected $_name = 'paidaccess_tariffs';

    protected $_rowClass = __NAMESPACE__.'\\row_tariff';

    protected $_primary = ['id'];

    protected $_referenceMap = [
        'TariffPlan' => [
            self::COLUMNS           => 'plan_id',
            self::REF_TABLE_CLASS   => __NAMESPACE__.'\\table_plans',
            self::REF_COLUMNS       => 'id'
        ],
    ];

    const FK_PLAN = __NAMESPACE__.'\\table_tariffs.TariffPlan';

    function fetchAllActive() {
        return $this->fetchAll('is_active = 1');
    }

}