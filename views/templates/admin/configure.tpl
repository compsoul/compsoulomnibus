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

<div class="panel">
	<h3><i class="icon icon-credit-card"></i> {l s='Compsoul Omnibus' mod='compsoulomnibus'}</h3>
	
	<p>{l s='The Omnibus module for PrestaShop is available for free distribution and editing, provided that the author is appropriately credited. However, if you require professional support or implementation services, you can purchase them from https://shop.compsoul.pl/en/. The module is designed to display the lowest product price within 30 days of a price change and store the price history of all scanned products. The module does not store prices for specific customer groups or countries. Please note that the current version is a test version, not a production version, and should be used at your own risk. The lack of a module on the site or incorrect display of the price may result in a fine of up to 40,000 PLN.' mod='compsoulomnibus'}</p>
	<p>{l s='Module functionalities include displaying the lowest product price within 30 days of a price change, displaying price change history on the backend, scanning and deleting price history for products, storing price history for variants, and scanning a specific product or all products after a price change. To install the Omnibus module for PrestaShop, download the package and install it through the module manager in the back office of your store. For correct price scanning, select the "Update prices" option in the modules configuration panel. If you need help with installation or configuration, please contact us.' mod='compsoulomnibus'}</p>
	<p>{l s='Please note that the above information is current as of March 2023 and is subject to change.' mod='compsoulomnibus'}</p>
	
</div>

<div class="panel">
	<h3><i class="icon icon-tags"></i> {l s='Documentation' mod='compsoulomnibus'}</h3>
	<p>
		&raquo; {l s='You can read the documentation on my blog' mod='compsoulomnibus'} :
		<ul>
			<li><a href="https://compsoul.dev/omnibus-module-prestashop/" target="_blank">{l s='English' mod='compsoulomnibus'}</a></li>
			<li><a href="https://compsoul.pl/modul-prestashop-omnibus/" target="_blank">{l s='Polish' mod='compsoulomnibus'}</a></li>
		</ul>
	</p>
</div>

{if $tests}
<div class="panel">
	<h3><i class="icon icon-check"></i> {l s='Tests' mod='compsoulomnibus'}</h3>
	{$tests}
</div>
{/if}
