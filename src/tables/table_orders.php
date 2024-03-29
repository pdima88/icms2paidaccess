<?php
namespace pdima88\icms2paidaccess\tables;

use modelUsers;
use pdima88\icms2ext\Format;
use pdima88\icms2ext\Table;
use pdima88\icms2paidaccess\model as modelPaidaccess;
use pdima88\icms2pay\tables\row_invoice;
use pdima88\icms2pay\tables\table_invoices;
use cmsUser;
use cmsModel;
use Exception;
use Zend_Db_Table_Row_Abstract;
use tableUsers;
use cmsDatabase;

/**
 * @property int $id ID заказа
 * @property int $user_id ID пользователя создавшего заказ
 * @property cmsUser $user Пользователь, создавший заказ
 * @property int $tariff_id ID тарифа
 * @property row_tariff $tariff Тариф
 * @property int $plan_id ID тарифного плана
 * @property row_plan $plan Тарифный план
 * @property double $amount Сумма
 * @property string $discount Скидка
 * @property double $total_amount Сумма с учетом скидки
 * @property int $bonuscode_id ID бонус-кода
 * @property int $invoice_id ID счета
 * @property row_invoice $invoice Счет
 * @property int $questions Количество оставшихся бонусных вопросов
 * @property array $groups Группы, в которые записывается пользователь при активации (фиксируется при активации)
 * @property int $period Срок доступа в днях (фиксируется при активации)
 * @property string $date_created Дата создания заказа
 * @property string $date_paid Дата оплаты заказа
 * @property string $date_activated Дата активации заказа
 * @property string $date_start Начало срока действия (Дата активации или дата окончания последней активированной подписки с этим тарифом)
 * @property string $date_expiry Окончание срока действия
 * @property string $date_cancelled Дата отмены заказа
 * @property bool $is_active Активен ли заказ (заказ активирован и у него не истек срок действия)
 * @property string $pay_type Тип оплаты (см. {@see tablePaidaccess_Orders::$payTypes})
 * @property int $level Уровень доступа
 *
 * @method table_orders getTable()
 */
class row_order extends Zend_Db_Table_Row_Abstract {

    function __get($columnName)
    {
        if ($columnName == 'plan') {
            return $this->findParentRow(__NAMESPACE__.'\\table_plans');
        }
        if ($columnName == 'tariff') {
            return $this->findParentRow(__NAMESPACE__.'\\table_tariffs');
        }
        if ($columnName == 'user') {
            return $this->findParentRow('\\tableUsers');
        }
        if ($columnName == 'invoice') {
            return $this->findParentRow(table_invoices::instance());
        }
        if ($columnName == 'groups') {
            return cmsModel::yamlToArray($this->_data['groups']);
        }
        return parent::__get($columnName); // TODO: Change the autogenerated stub
    }

    function activate() {
        $this->getTable()->activateOrder($this);
    }

    function makeInvoice() {
        return $this->getTable()->makeInvoice($this);
    }

    function setDiscount($discount, $totalAmount) {
        $this->total_amount = $totalAmount;
        $this->discount = (isset($discount) && $discount !== '') ? $discount : ($this->amount - $totalAmount);
    }

    function getInvoiceTitle() {
        $plan = $this->plan;
        if ($plan) {
            return $plan->title.' ('.Format::formatDuration($this->period).')';
        }
        return '';
    }
}

/**
 * CREATE TABLE `cms_paidaccess_orders` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `tariff_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `plan_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `amount` DECIMAL(15,2) UNSIGNED NOT NULL DEFAULT '0.00',
    `discount` DECIMAL(15,2) UNSIGNED NOT NULL DEFAULT '0.00',
    `total_amount` DECIMAL(15,2) UNSIGNED NOT NULL DEFAULT '0.00',
    `bonuscode_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `invoice_id` INT(10) UNSIGNED NULL DEFAULT '0',
    `groups` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `period` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `date_created` DATETIME NULL DEFAULT NULL,
    `date_paid` DATETIME NULL DEFAULT NULL,
    `date_activated` DATETIME NULL DEFAULT NULL,
    `date_start` DATETIME NULL DEFAULT NULL,
    `date_expiry` DATETIME NULL DEFAULT NULL,
    `is_active` TINYINT(4) NULL DEFAULT NULL,
    `pay_type` VARCHAR(50) NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
    )
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB
    ;

 *
 * @method static row_order getById($id) Возвращает заказ по ID
 * @method row_order createRow(array $data = [], $defaultSource = null)
 */
class table_orders extends Table {
    protected $_name = 'paidaccess_orders';

    protected $_rowClass = __NAMESPACE__.'\\row_order';

    protected $_primary = ['id'];

    protected $_referenceMap = [
        'Tariff' => [
            self::COLUMNS           => 'tariff_id',
            self::REF_TABLE_CLASS   => __NAMESPACE__.'\\table_tariffs',
            self::REF_COLUMNS       => 'id'
        ],
        'TariffPlan' => [
            self::COLUMNS           => 'plan_id',
            self::REF_TABLE_CLASS   => __NAMESPACE__.'\\table_plans',
            self::REF_COLUMNS       => 'id'
        ],
        'User' => [
            self::COLUMNS           => 'user_id',
            self::REF_TABLE_CLASS   => '\\tableUsers',
            self::REF_COLUMNS       => 'id'
        ],
        'Invoice' => [
            self::COLUMNS           => 'invoice_id',
            self::REF_TABLE_CLASS   =>  'pdima88\\icms2pay\\tables\\table_invoices',
            self::REF_COLUMNS       => 'id'
        ]
    ];

    const FK_TARIFF = __CLASS__.'.Tariff';
    const FK_PLAN = __CLASS__.'.TariffPlan';
    const FK_USER = __CLASS__.'.User';
    const FK_INVOICE = __CLASS__.'.Invoice';

    const PAY_TYPE_FREE = 'free';
    const PAY_TYPE_BONUS = 'bonus';
    const PAY_TYPE_ADMIN = 'admin';
    const PAY_TYPE_PAY = 'pay';
    const PAY_TYPE_PAYME = 'payme';
    const PAY_TYPE_CLICK = 'click';
    const PAY_TYPE_RECEIPT = 'receipt';
    const PAY_TYPE_TRANSFER = 'transfer';

    static $payTypes = [
        self::PAY_TYPE_FREE => 'Бесплатно',
        self::PAY_TYPE_BONUS => 'Бесплатно (Использован бонус-код)',
        self::PAY_TYPE_ADMIN => 'Бесплатно (Активирован администратором)',
        self::PAY_TYPE_PAY => 'Ожидает оплаты',
        self::PAY_TYPE_PAYME => 'Payme',
        self::PAY_TYPE_CLICK => 'Click',
        self::PAY_TYPE_RECEIPT => 'Квитанция в банк',
        self::PAY_TYPE_TRANSFER => 'Перечислением',
    ];

    function findNotPaid($tariffId, $userId = null) {
        /** @var row_order $order */
        $order = $this->fetchRow([
            'tariff_id = ?' => $tariffId,
            'date_paid IS NULL AND date_activated IS NULL AND date_cancelled IS NULL AND user_id = ?' => $userId ?? cmsUser::getInstance()->id,
        ]);
        if ($order) {
            $invoice = $order->invoice;
            if ($invoice) {
                if ($invoice->status == 0 &&
                    $invoice->pay_type == ''
                ) return $order;
                return null;
            } else {
                return $order;
            }
        } else {
            return null;
        }
    }

    /**
     * Creates new order from current user and specified tariff
     * @param row_tariff $tariff
     * @return row_order
     */
    function make($tariff, $userId = null) {
        $order = $this->createRow();
        $order->user_id = $userId ?? cmsUser::getInstance()->id;
        $order->tariff_id = $tariff->id;
        $order->plan_id = $tariff->plan_id;
        $order->amount = $tariff->price;
        $order->total_amount = $order->amount;

        $tariffPlan = $tariff->plan;
        if ($tariffPlan) {
            $order->groups = cmsModel::arrayToYaml($tariffPlan->groups);
            $order->level = $tariffPlan->level;
        }
        $order->questions = $tariff->questions;

        $order->period = $tariff->period;
        $order->date_created = now();

        return $order;
    }

    /**
     * @param row_order $order
     */
    function activateOrder($order) {
        if ($order->date_activated) return false;
        $user = tableUsers::getById($order->user_id);
        if (!$user) return false;
        $is_transaction = false;
        if (cmsDatabase::getInstance()->isAutocommitOn()) {
            cmsDatabase::getInstance()->beginTransaction();
            $is_transaction = true;
        }
        $order->date_activated = now();
        $order->date_expiry = datetime_iso(strtotime(' +'.$order->period.' days'));
        $order->is_active = true;
        $order->save();
        $model = modelPaidaccess::getInstance();
        $model->refreshByUserId($user->id);

        if (!empty($order->groups)) {
            $groups = array_unique(array_merge( $user->groups, $order->groups));
            $diff = array_diff($groups, $user->groups);
            if (!empty($diff)) {
                modelUsers::getInstance()->updateUser($user->id, ['groups' => $groups]);
            }
        }

        if ($is_transaction) {
            cmsDatabase::getInstance()->commit();
        }
    }

    /**
     * @param paidaccessOrder $order
     * @return row_invoice
     */
    function makeInvoice($order) {
        if ($order->date_paid) throw new Exception('Can`t create invoice for paid order');
        $invoice = $order->invoice;
        $items = [
            [
                $order->getInvoiceTitle(),
                $order->amount
            ],
        ];
        if ($order->amount != $order->total_amount) {
            $items[] = [
                'Скидка (' . $order->discount . ')',
                $order->total_amount - $order->amount
            ];
        }
        if (!$invoice) {
            $invoice = table_invoices::instance()->make($order->total_amount, $order->getInvoiceTitle(), 'paidaccess', [
                'items' => $items,
                'order_id' => $order->id
            ]);
            $order->invoice_id = $invoice->save();
        } else {
            if ($invoice->order_id != $order->id || $invoice->controller != 'paidaccess' || $invoice->user_id != $order->user_id) {
                // TODO: log warning: invalid invoice_id has been set up in order
                $invoice = table_invoices::instance()->make($order->total_amount, $order->getInvoiceTitle(), 'paidaccess', [
                    'items' => $items,
                    'order_id' => $order->id
                ]);
                $order->invoice_id = $invoice->save();
            }
            if ($invoice->status != 0) {
                throw new Exception('Can`t make invoice: already exists');
            }
            $invoice->title = $order->getInvoiceTitle();
            $invoice->amount = $order->total_amount;
            $invoice->data = [
                'items' => $items,
            ];
        }
        return $invoice;
    }

    function setExpiried($userId = null) {
        $where = [
            'is_active = 1 AND date_expiry <= ?' => now()
        ];
        if ($userId) $where['user_id = ?'] = $userId;
        $this->update(['is_active' => 0], $where);
    }

}