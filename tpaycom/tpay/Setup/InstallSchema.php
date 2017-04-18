<?php
/**
 * @package   tpaycom\tpay
 * @author    Oleksandr Yeremenko <oyeremenko@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license   See LICENSE_DIVANTE.txt for license details.
 */

namespace tpaycom\tpay\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use tpaycom\tpay\Api\TpayInterface;

/**
 * Class InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();

        $connection->addColumn(
            $setup->getTable('quote'),
            TpayInterface::UNIQUE_MD5_KEY,
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => true,
                'comment'  => 'MD5 hash of TPay transaction',
            ]
        );

        $connection->addIndex(
            $setup->getTable('quote'),
            $setup->getIdxName(
                $setup->getTable('quote'),
                TpayInterface::UNIQUE_MD5_KEY,
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            TpayInterface::UNIQUE_MD5_KEY,
            AdapterInterface::INDEX_TYPE_UNIQUE
        );

        $connection->addColumn(
            $setup->getTable('sales_order'),
            TpayInterface::UNIQUE_MD5_KEY,
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => true,
                'comment'  => 'MD5 hash of TPay transaction',
            ]
        );

        $connection->addIndex(
            $setup->getTable('sales_order'),
            $setup->getIdxName(
                $setup->getTable('quote'),
                TpayInterface::UNIQUE_MD5_KEY,
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            TpayInterface::UNIQUE_MD5_KEY,
            AdapterInterface::INDEX_TYPE_UNIQUE
        );
    }
}
