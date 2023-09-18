{*
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
*}

<h2>{l s='Compsoul Omnibus Price' mod='compsoulomnibus'}</h2>

<div class="form-group">
    <label class="form-check-label">
        <input type="checkbox" id="compsoul_omnibus_upsert" name="compsoul_omnibus_upsert" checked="false" autocomplete="off">
        {l s='Updates omnibus price.' mod='compsoulomnibus'}
    </label>
    <div class="alert alert-info" role="alert">
        <p class="alert-text">
            {l s='Add the current price to the price history. Before adding the price to the list, set all price variants and modifiers. Then save the product, come back here, check this option and save the product again.' mod='compsoulomnibus'}
        </p>
    </div>
</div>

<table class="table">
    <thead>
        <tr>
            <th>
                {l s='ID' mod='compsoulomnibus'}<br>
                (id_compsoulomnibus)
            </th>
            <th>
                {l s='ID Product' mod='compsoulomnibus'}<br>
                (id_product)
            </th>
            <th>
                {l s='ID Product Attribute' mod='compsoulomnibus'}<br>
                (id_product_attribute)
            </th>
            <th>
                {l s='Is the price active?' mod='compsoulomnibus'}<br>
                (is_active)
            </th>
            <th>
                {l s='Saved price' mod='compsoulomnibus'}<br>
                (omnibus_price)
            </th>
            <th>
                {l s='Date when price was replaced' mod='compsoulomnibus'}<br>
                (omnibus_date)
            </th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$omnibus item=item}
            <tr>
                <td>{$item.id_compsoulomnibus}</td>
                <td>{$item.id_product}</td>
                <td>{$item.id_product_attribute}</td>
                <td>{$item.is_active}</td>
                <td>{$item.omnibus_price}</td>
                <td>{$item.omnibus_date}</td>
            </tr>
        {/foreach}
    </tbody>
</table>

<div class="form-group">
    <label class="form-check-label">
        <input type="checkbox" id="compsoul_omnibus_delete" name="compsoul_omnibus_delete" checked="false" autocomplete="off">
        {l s='Delete price history for this product.' mod='compsoulomnibus'}
    </label>
    <div class="alert alert-danger" role="alert">
        <p class="alert-text">
            {l s='When deleting your price history, make sure that the current price has been displayed for at least 30 days or not at all. Otherwise, you risk financial penalties.' mod='compsoulomnibus'}
        </p>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#compsoul_omnibus_upsert, #compsoul_omnibus_delete').prop('checked', false);
    });
</script>
