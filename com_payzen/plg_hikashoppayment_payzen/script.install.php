<?php
/**
 * Copyright © Lyra Network.
 * This file is part of PayZen plugin for HikaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License (GPL v3)
 */

defined('_JEXEC') or die('Restricted access');

// Use modern Joomla\Filesystem\File for Joomla 6 (CMS namespace is deprecated)
if (! class_exists('\\Joomla\\Filesystem\\File')) {
    class_alias('\\Joomla\\CMS\\Filesystem\\File', '\\Joomla\\Filesystem\\File');
}
use Joomla\Filesystem\File;

// Use InstallerScriptBase if available (Joomla 4+), otherwise use base installer script
/**
 * Compatibility base class for the installer script.
 *
 * Extends {@see \Joomla\CMS\Installer\InstallerScriptBase} on Joomla 4+ and
 * falls back to an empty class on earlier versions.
 */
if (class_exists('\\Joomla\\CMS\\Installer\\InstallerScriptBase')) {
    require_once JPATH_LIBRARIES . '/src/CMS/Installer/InstallerScriptBase.php';
    class plghikashoppaymentpayzenInstallerScriptBase extends \Joomla\CMS\Installer\InstallerScriptBase {}
} else {
    class plghikashoppaymentpayzenInstallerScriptBase {}
}

class plghikashoppaymentpayzenInstallerScript extends plghikashoppaymentpayzenInstallerScriptBase
{
    /**
     * Called after any type of action.
     *
     * @param string $route Which action is happening (install|uninstall|discover_install|update)
     * @param JAdapterInstance $adapter The object responsible for running this script
     *
     * @return boolean True on success
     */
    function postflight($route, $adapter)
    {
        return true;
    }

    /**
     * Called before any type of action.
     *
     * @param string $route Which action is happening (install|uninstall|discover_install|update)
     * @param JAdapterInstance $adapter The object responsible for running this script
     *
     * @return boolean True on success
     */
    function preflight($type, $adapter)
    {
        if ($type === 'uninstall') {
            require_once rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_hikashop' .
                DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php';

            $targetFolder = HIKASHOP_IMAGES . 'payment';

            if (File::exists($targetFolder . DIRECTORY_SEPARATOR . 'payzen_cards.png')) {
                File::delete($targetFolder . DIRECTORY_SEPARATOR . 'payzen_cards.png');
            }

            if (File::exists($targetFolder . DIRECTORY_SEPARATOR . 'payzen.png')) {
                File::delete($targetFolder . DIRECTORY_SEPARATOR . 'payzen.png');
            }
        }

        return true;
    }
}
