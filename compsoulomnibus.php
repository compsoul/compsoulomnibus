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

if (!defined('_PS_VERSION_')) {
    exit;
}
 
class Compsoulomnibus extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'compsoulomnibus';
        $this->tab = 'administration';
        $this->version = '1.0.1';
        $this->author = 'compsoul.dev';
        $this->need_instance = 1;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Compsoul Omnibus');
        $this->description = $this->l('The Omnibus module displays the lowest recorded price of a product in the last 30 days.');

        $this->confirmUninstall = $this->l('Uninstalling the module results in deleting the price history for all products. The change is permanent and cannot be reversed without restoring the database backup.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('actionProductSave') &&
            $this->registerHook('displayCompsoulOmnibusPrice') &&
            $this->registerHook('displayAdminProductsPriceStepBottom');
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    public function hookActionProductSave($params)
    {
        if(Tools::getValue('compsoul_omnibus_upsert')) {
            $this->upsertOmnibusProduct($params['id_product']);
            $this->upsertOmnibusProductCombinations($params['id_product']);
        }

        if(Tools::getValue('compsoul_omnibus_delete')) {
            $this->deleteOmnibusProducts($params['id_product']);
        }
    }

    public function hookDisplayCompsoulOmnibusPrice($params)
    {
        $product = $params['product'];

        $id_product = (isset($product['id_product'])) ? $product['id_product'] : 0;
        $id_product_attribute = (isset($product['id_product_attribute'])) ? $product['id_product_attribute'] : 0;

        $omnibus = $this->getOmnibus($id_product, $id_product_attribute);

        $this->context->smarty->assign([
          'price' => $this->getOmnibusPrice($omnibus),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/compsoul-omnibus-price.tpl');
    }

    public function hookDisplayAdminProductsPriceStepBottom($params)
    {
        $this->context->smarty->assign([
          'omnibus' => $this->getOmnibusById($params['id_product']),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/products-price-step.tpl');
    }

    public function getContent()
    {
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('tests', $this->tests());

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->afterSubmit().$this->renderForm();
    }

    protected function afterSubmit()
    {
        if (((bool)Tools::isSubmit('submitCompsoulomnibusModule')) == true) {
            return $this->postProcess();
        }
    }

    protected function tests()
    {
        $compsoulomnibus_tests = Tools::getValue('COMPSOULOMNIBUS_TESTS');

        if ($compsoulomnibus_tests) {
            ob_start();
                include(dirname(__FILE__).'/tests.php');
                $contents = ob_get_contents();
            ob_end_clean();
        }

        return ($compsoulomnibus_tests) ? $contents : null;
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCompsoulomnibusModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Add omnibus prices'),
                        'name' => 'COMPSOULOMNIBUS_ADD',
                        'is_bool' => true,
                        'desc' => $this->l('It scans all products and adds the current price to the price history.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Update prices'),
                        'name' => 'COMPSOULOMNIBUS_UPDATE',
                        'is_bool' => true,
                        'desc' => $this->l('Updates all products and their variants adding the current price to the history.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Time to do some cleaning'),
                        'name' => 'COMPSOULOMNIBUS_CLEAN',
                        'is_bool' => true,
                        'desc' => $this->l('Delete the history of all non-existent variants or products.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Module tests'),
                        'name' => 'COMPSOULOMNIBUS_TESTS',
                        'is_bool' => true,
                        'desc' => $this->l('Perform module testing'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'message',
                        'label' => 'Warning',
                        'name' => 'COMPSOULOMNIBUS_DANGER',
                        'desc' => '<div class="alert alert-danger">'.$this->l('Removes price history from all products and their variants. Use this option carefully, as you may expose yourself to financial penalties.').'</div>',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Delete all prices'),
                        'name' => 'COMPSOULOMNIBUS_RESET',
                        'is_bool' => true,
                        'desc' => $this->l('The use of this option is irreversible.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        return array(
            'COMPSOULOMNIBUS_ADD' => false,
            'COMPSOULOMNIBUS_UPDATE' => false,
            'COMPSOULOMNIBUS_TESTS' => false,
            'COMPSOULOMNIBUS_CLEAN' => false,
            'COMPSOULOMNIBUS_RESET' => false,
        );
    }

    protected function postProcess()
    {
        $compsoulomnibus_add = Tools::getValue('COMPSOULOMNIBUS_ADD');
        $compsoulomnibus_update = Tools::getValue('COMPSOULOMNIBUS_UPDATE');
        $compsoulomnibus_clean = Tools::getValue('COMPSOULOMNIBUS_CLEAN');
        $compsoulomnibus_reset = Tools::getValue('COMPSOULOMNIBUS_RESET');

        if ($compsoulomnibus_add || $compsoulomnibus_update) {
            $this->omnibusProducts();
        }

        if ($compsoulomnibus_reset) {
            $this->deleteOmnibusProducts();
        }

        if ($compsoulomnibus_clean) {
            $this->cleanOmnibus();
        }

        $confirmation = $this->l('The settings have been updated.');
        $confirmation = ($compsoulomnibus_add || $compsoulomnibus_update) ? $confirmation . '<br>' . $this->l('The active price of the product has been added to the product history. If it is the lowest price for 30 days it will be displayed.') : $confirmation;
        $confirmation = ($compsoulomnibus_clean) ? $confirmation . '<br>' . $this->l('You cleaned up the database a bit, bravo!') : $confirmation;
        $confirmation = ($compsoulomnibus_reset) ? $confirmation . '<br>' . $this->l('Price history has been removed, this change is irreversible make sure your products on promotion have the lowest price for 30 days.') : $confirmation;

        return $this->displayConfirmation($confirmation);
    }

    protected function omnibusProducts()
    {
        $products = Product::getProducts($this->context->language->id, 0, 0, 'id_product', 'ASC');
        foreach ($products as $product) {
          $this->upsertOmnibusProduct($product['id_product']);
          $this->upsertOmnibusProductCombinations($product['id_product']);
        }
    }

    protected function deleteOmnibusProducts($id_product = 0)
    {
        if($id_product == 0) {
            (Db::getInstance())->delete('compsoulomnibus');
        } else {
            (Db::getInstance())->delete('compsoulomnibus', 'id_product = ' . (int)$id_product);
        }
    }

    protected function upsertOmnibusProduct($id_product = null)
    {
        if (!isset($id_product)) {
            throw new Exception('Missing required parameters $id_product');
        }

        $product = new Product($id_product);
        $decimals = Configuration::get('PS_PRICE_DISPLAY_PRECISION');
        $price = $product->getPrice();

        $omnibus = $this->getOmnibus($id_product);
        $omnibus_price = $this->getOmnibusActivePrice($omnibus);
        $omnibus_price_converted = Tools::ps_round((float) $omnibus_price, $decimals);

        if($omnibus_price_converted !== $price) {
            $this->toggleActiveOmnibus([
                'id_product' => $id_product,
                'id_product_attribute' => 0,
                'omnibus_price' => $price,
            ]);
        }
    }

    protected function upsertOmnibusProductCombinations($id_product = null)
    {
        if (!isset($id_product)) {
            throw new Exception('Missing required parameters $id_product');
        }

        $product = new Product($id_product);
        $decimals = Configuration::get('PS_PRICE_DISPLAY_PRECISION');
        $combinations = $product->getAttributeCombinations($this->context->language->id);

        foreach ($combinations as $combination) {
            $id_product_attribute = $combination['id_product_attribute'];
            $price = $product->getPrice(true, $id_product_attribute);

            $omnibus = $this->getOmnibus($id_product, $id_product_attribute);
            $omnibus_price = $this->getOmnibusActivePrice($omnibus);
            $omnibus_price_converted = Tools::ps_round((float) $omnibus_price, $decimals);

            if($omnibus_price_converted !== $price) {
                $this->toggleActiveOmnibus([
                    'id_product' => $id_product,
                    'id_product_attribute' => $id_product_attribute ,
                    'omnibus_price' => $price,
                ]);
            }
        }
    }

    protected function getOmnibus($id_product = 0, $id_product_attribute = 0)
    {
        $omnibus = Db::getInstance()->executeS((new DbQuery())
            ->select('*')
            ->from('compsoulomnibus')
            ->where('id_product = ' . (int)$id_product)
            ->where('id_product_attribute = ' . (int)$id_product_attribute)
        );

        return $omnibus;
    }

    protected function getOmnibusById($id_product = 0)
    {
        $omnibus = Db::getInstance()->executeS((new DbQuery())
            ->select('*')
            ->from('compsoulomnibus')
            ->where('id_product = ' . (int)$id_product)
        );

        return $omnibus;
    }

    protected function toggleActiveOmnibus($data)
    {
        $this->unsetActiveOmnibus([
            'id_product' => $data['id_product'],
            'id_product_attribute' => $data['id_product_attribute'],
            'is_active' => 0,
            'omnibus_date' => $this->getCurrentDate(),
        ]);

        $this->insertOmnibusRow([
            'id_product' => $data['id_product'],
            'id_product_attribute' => $data['id_product_attribute'],
            'is_active' => 1,
            'omnibus_price' => $data['omnibus_price'],
        ]);
    }

    protected function unsetActiveOmnibus($data)
    {
        $id_product = $data['id_product'];
        $id_product_attribute = $data['id_product_attribute'];

        $result = Db::getInstance()->update('compsoulomnibus', $data, 'id_product =' . $id_product . ' AND id_product_attribute =' . $id_product_attribute . ' AND is_active = 1');
    }

    protected function getOmnibusPrice($omnibus)
    {
        $omnibus_price = -1;

        foreach ($omnibus as $row) {
            $price = $row['omnibus_price'];
            $data = $row['omnibus_date'];
            $isDateValid = $this->isDateValid($data, 30);

            if($isDateValid && ($price < $omnibus_price || $omnibus_price < 0)) {
                $omnibus_price = $price;
            }

        }

        return $omnibus_price;
    }

    protected function getOmnibusActivePrice($omnibus)
    {
        $omnibus_price = -1;

        foreach ($omnibus as $row) {
            if($row['is_active'] == 1) {
                $omnibus_price = $row['omnibus_price'];
            }
        }

        return $omnibus_price;
    }

    protected function isOmnibusRow($id_product = 0, $id_product_attribute = 0)
    {
        if ($id_product === null) {
            throw new Exception('$id_product parameters is required.');
        }

        $result = (Db::getInstance())->executeS((new DbQuery())
            ->from('compsoulomnibus')
            ->select('Count(*)')
            ->where('id_product = ' . (int)$id_product)
            ->where('id_product_attribute = ' . (int)$id_product_attribute)
        );

        $count = array_shift($result[0]);

        return $count > 0;
    }

    protected function insertOmnibusRow($data = [])
    {
        $result = Db::getInstance()->insert('compsoulomnibus', $data);
    }

    protected function cleanOmnibus($id_product = 0)
    {
        $query = new DbQuery();
        $query->from('compsoulomnibus')->select('*');

        if($id_product != 0) {
            $query->where('id_product = ' . (int)$id_product);
        }

        $result = (Db::getInstance())->executeS($query);

        foreach ($result as $row) {
            $product_id = $row['id_product'];
            $product_attribute_id = $row['id_product_attribute'];

            $product = new Product($product_id);
            $combination = new Combination($product_attribute_id);

            if(!$product->id) {
                (Db::getInstance())->delete('compsoulomnibus', 'id_product = ' . (int)$product_id);
            } elseif (!$combination->id_product && $product_attribute_id != 0) {
                (Db::getInstance())->delete('compsoulomnibus', 'id_product_attribute = ' . (int)$product_attribute_id);
            }
        }
    }

    protected function getCurrentDate() 
    {
        return date('Y-m-d H:i:s');
    }

    protected function isDateValid($date, $days) 
    {
        $interval = $this->getDaysSinceDate($date);

        return ($interval < $days || (int) $date === 0);
    }

    protected function getDaysSinceDate($date) 
    {
        $origin = strtotime($date);
        $current = time();

        $days = abs($current - $origin) / 86400;

        return $days;
    }
}
