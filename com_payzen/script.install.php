<?php
/**
 * PayZen V2-Payment Module version 2.0.0 for HikaShop 2.x-3.x. Support contact : support@payzen.eu.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Lyra Network (http://www.lyra-network.com/)
 * @copyright 2014-2017 Lyra Network and contributors
 * @license   http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL v3)
 * @category  payment
 * @package   payzen
 */
defined('_JEXEC') or die('Restricted access');

class com_payzenInstallerScript
{

    function install()
    {
        JInstaller::getInstance()->install(realpath(dirname(__FILE__)) . DS . 'plg_hikashoppayment_payzen');
        JInstaller::getInstance()->install(realpath(dirname(__FILE__)) . DS . 'plg_hikashoppayment_payzenmulti');
        JInstaller::getInstance()->install(realpath(dirname(__FILE__)));
    }
}

// Joomla 1.5
function com_install()
{
    $installClass = new com_payzenInstallerScript();
    $installClass->install();
}
