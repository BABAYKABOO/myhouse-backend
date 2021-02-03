<?php

use yii\db\Migration;

/**
 * Class m180524_093946_service_invoice
 */
class m180524_093946_service_invoice extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = "
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

TRUNCATE `invoice_service`;
TRUNCATE `invoice`;
TRUNCATE `tariff_service`;
TRUNCATE `tariff`;
TRUNCATE `service`;
TRUNCATE `service_unit`;

INSERT INTO `service_unit` (`id`, `name`) VALUES 
(1, 'кВт/ч'), 
(2, 'куб.м'),
(3, 'кв.м/кКал'),
(4, 'кв.м'),
(5, 'ед.');

INSERT INTO `service` (`id`, `name`, `service_unit_id`) VALUES 
(1, 'Электроэнергия', 1), 
(2, 'Холодная вода', 2),
(3, 'Отопление', 3),
(4, 'Горячая вода', 2);

INSERT INTO `tariff` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES 
(1, 'Тариф #1', 'Основной тариф, применяется ко все пользователям, если у них нет льгот.', 1527153000, 1527153000), 
(2, 'Тариф #2', 'Пенсионный тариф, применяется ко всем одиноким пенсионерам живущим в доме.', 1527154000, 1527154000),
(3, 'Тариф #3', 'Дополнительный тариф, применяется к жильцам имеющим дополнительные льготы.', 1527155000, 1527155000);

INSERT INTO `tariff_service` (`id`, `price_unit`, `tariff_id`, `service_id`) VALUES 
(1, '5.00', 1, 1), 
(2, '8.00', 1, 2),
(3, '15.00', 1, 3),
(4, '24.00', 1, 4),
(5, '2.00', 2, 1), 
(6, '3.00', 2, 2),
(7, '7.00', 2, 3),
(8, '3.00', 3, 1), 
(9, '4.00', 3, 2),
(10, '15.00', 3, 3),
(11, '24.00', 3, 4);

INSERT INTO `invoice` (`id`, `uid`, `uid_date`, `period_start`, `period_end`, `status`, `created_at`, `updated_at`, `flat_id`, `tariff_id`) VALUES
(1, '00001', '2017-12-01', '2017-11-01', '2017-11-30', 10, 1498867149, 1498867149, 1, 1),
(2, '00002', '2017-12-01', '2017-11-01', '2017-11-30', 10, 1505701149, 1505701149, 2, 1),
(3, '00003', '2017-12-01', '2017-11-01', '2017-11-30', 10, 1505889600, 1505889600, 9, 1),
(4, '00004', '2018-01-01', '2017-12-01', '2017-12-31', 10, 1505959245, 1505959245, 1, 1),
(5, '00005', '2018-02-01', '2018-01-01', '2018-01-31', 10, 1506001149, 1506001149, 7, 1),
(6, '00006', '2018-02-01', '2018-01-01', '2018-01-31', 10, 1506101149, 1506101149, 1, 1),
(7, '00007', '2018-03-15', '2018-02-01', '2018-02-28', 10, 1506201149, 1506201149, 7, 1),
(8, '00008', '2018-04-15', '2018-03-01', '2018-03-31', 10, 1506324000, 1506324000, 7, 3),
(9, '00009', '2018-05-15', '2018-04-01', '2018-04-30', 10, 1506429600, 1506429600, 7, 3),
(10, '000010', '2018-03-15', '2018-02-01', '2018-02-28', 10, 1506519384, 1506519384, 1, 1),
(11, '000011', '2018-04-15', '2018-03-01', '2018-03-31', 10, 1506630000, 1506630000, 1, 1),
(12, '000012', '2018-04-20', '2018-04-01', '2018-04-15', 10, 1506635000, 1506635000, 1, 1),
(13, '000013', '2018-05-15', '2018-04-15', '2018-04-30', 10, 1506739600, 1506739600, 1, 1);

INSERT INTO `invoice_service` (`id`, `invoice_id`, `amount`, `service_id`) VALUES 
(1, 1, '50.00', 1), 
(2, 1, '100.00', 2),
(3, 1, '200.00', 3),
(4, 1, '50.00', 4),
(5, 2, '50.00', 1), 
(6, 2, '100.00', 2),
(7, 2, '200.00', 3),
(8, 2, '50.00', 4),
(9, 3, '50.00', 1), 
(10, 3, '100.00', 2),
(11, 3, '200.00', 3),
(12, 3, '50.00', 4),
(13, 4, '60.00', 1), 
(14, 4, '120.00', 2),
(15, 4, '240.00', 3),
(16, 4, '55.00', 4),
(17, 5, '20.00', 1), 
(18, 5, '70.00', 2),
(19, 5, '200.00', 3),
(20, 5, '30.00', 4),
(21, 6, '55.00', 1), 
(22, 6, '100.00', 2),
(23, 6, '210.00', 3),
(24, 6, '50.00', 4),
(25, 7, '50.00', 1), 
(26, 7, '90.00', 2),
(27, 7, '160.00', 3),
(28, 7, '40.00', 4),
(29, 8, '40.00', 1), 
(30, 8, '90.00', 2),
(31, 8, '170.00', 3),
(32, 8, '30.00', 4),
(33, 9, '50.00', 1), 
(34, 9, '100.00', 2),
(35, 9, '200.00', 3),
(36, 9, '50.00', 4),
(37, 10, '50.00', 1), 
(38, 10, '100.00', 2),
(39, 10, '200.00', 3),
(40, 10, '50.00', 4),
(41, 11, '70.00', 1), 
(42, 11, '100.00', 2),
(43, 11, '250.00', 3),
(44, 11, '40.00', 4),
(45, 12, '60.00', 1), 
(46, 12, '110.00', 2),
(47, 12, '200.00', 3),
(48, 12, '50.00', 4),
(49, 13, '50.00', 1), 
(50, 13, '100.00', 2),
(51, 13, '230.00', 3),
(52, 13, '70.00', 4);

INSERT INTO `account_transaction` (`id`, `date_pay`, `type`, `status`, `pay_purpose`, `amount`, `currency_id`, `account_id`) VALUES 
(1, '2018-03-20', 'in', 10, 1, '1000.00', 1, 40),
(2, '2018-03-15', 'out', 10, 3, '4000.00', 1, NULL),
(3, '2018-03-20', 'in', 10, 1, '2000.00', 1, 46),
(4, '2018-04-10', 'in', 10, 1, '1000.00', 1, 40),
(5, '2018-04-20', 'in', 10, 1, '1300.00', 1, 40),
(6, '2018-04-15', 'out', 10, 3, '500.00', 1, NULL),
(7, '2018-04-20', 'in', 10, 1, '1000.00', 1, 46),
(8, '2018-04-23', 'in', 10, 1, '1000.00', 1, 40),
(9, '2018-05-10', 'in', 10, 1, '1000.00', 1, 40),
(10, '2018-05-15', 'out', 10, 3, '1400.00', 1, NULL),
(11, '2018-05-20', 'in', 10, 1, '500.00', 1, 40);



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
        ";
        
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $sql = "
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

TRUNCATE `invoice_service`;
TRUNCATE `invoice`;
TRUNCATE `tariff_service`;
TRUNCATE `tariff`;
TRUNCATE `service`;
TRUNCATE `service_unit`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
        ";
        
        $this->execute($sql);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180524_093946_service_invoice cannot be reverted.\n";

        return false;
    }
    */
}
