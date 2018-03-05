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

class plghikashoppaymentpayzenmultiInstallerScript
{

    /**
     * Called after any type of action.
     *
     * @param string $route Which action is happening (install|uninstall|discover_install|update)
     * @param JAdapterInstance $adapter The object responsible for running this script
     *
     * @return boolean True on success
     */
    function postflight($route, JAdapterInstance $adapter)
    {
        if ($route != 'install' && $route != 'update' && $route != 'discover_install') {
            return;
        }

        // get the client info
        jimport('joomla.application.helper');
        $client = JApplicationHelper::getClientInfo(- 1);

        // here we set the folder we are going to rename manifest from.
        if ($client) {
            $path = $adapter->getParent()->getPath('extension_' . $client->name);
        } else {
            $path = $adapter->getParent()->getPath('extension_root');
        }

        JFile::move('payzenmulti_j3.xml', 'payzenmulti.xml', $path);
    }

    function preflight($type, JAdapterInstance $adapter)
    {
        if ($type == 'uninstall') {
            include_once rtrim(JPATH_ADMINISTRATOR, DS) . DS . 'components' . DS . 'com_hikashop' . DS . 'helpers' . DS .
                 'helper.php';
            jimport('joomla.filesystem.folder');
            jimport('joomla.filesystem.file');

            $targetFolder = HIKASHOP_IMAGES . 'payment';

            if (JFile::exists($targetFolder . DS . 'payzenmulti_cards.png')) {
                JFile::delete($targetFolder . DS . 'payzenmulti_cards.png');
            }

            if (JFile::exists($targetFolder . DS . 'payzenmulti.png')) {
                JFile::delete($targetFolder . DS . 'payzenmulti.png');
            }
        }
    }
}
