<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'compsoulomnibus` (
    `id_compsoulomnibus` int(11) NOT NULL AUTO_INCREMENT,
    `id_product` int(10) unsigned NOT NULL,
    `id_product_attribute` int(10) unsigned NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 0,
    `omnibus_price` decimal(20,6) NOT NULL,
    `omnibus_date` datetime NOT NULL,
    PRIMARY KEY  (`id_compsoulomnibus`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

return Db::getInstance()->execute($query);
