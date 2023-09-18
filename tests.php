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

function it($desc, $fn) {
  try {
    $fn();
    success($desc);
  } catch (Exception $error) {
    desc($desc);
    error($error);
  }
}

function isEqual($expected, $actual) {
    if ($expected !== $actual) {
        throw new Exception('Expected '.$expected.', but got '.$actual);
    }
}

function isGreater($expected, $actual) {
    if ($expected > $actual) {
        throw new Exception('Expected to be more than '.$expected.', but got '.$actual);
    }
}

function isLess($expected, $actual) {
    if ($expected < $actual) {
        throw new Exception('Expected to be less than '.$expected.', but got '.$actual);
    }
}

function success($desc) {
    echo '<div class="alert alert-success">'. $desc .'</div>';
}

function desc($desc) {
    echo '<div class="alert alert-info">'. $desc .'</div>';
}

function error($error) {
    echo '<div class="alert alert-danger">'. $error .'</div>';
}

it('Current Date', function() {
    $date = new DateTime();
    isEqual($this->getCurrentDate(), $date->format('Y-m-d H:i:s'));
});

it('Is the number of days from the date 2023-01-01 00:00:00 greater than 64', function() {
    isGreater(64, $this->getDaysSinceDate('2023-01-01 00:00:00'));
});

it('Is the number of days since 2222-01-01 00:00:00 greater than 0', function() {
    isGreater(0, $this->getDaysSinceDate('2222-01-01 00:00:00'));
});

it('Number instead of a string, should start counting from the beginning of time :)', function() {
    isGreater(0, $this->getDaysSinceDate(200));
});

it('The number of day should be more than 30, the date was manually calculated', function() {
    isGreater(30, $this->getDaysSinceDate('2023-02-06 00:00:00'));
});
