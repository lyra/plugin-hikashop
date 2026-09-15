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

use Joomla\CMS\Factory;

// Use InstallerScriptBase if available (Joomla 4+), otherwise use base installer script
/**
 * Compatibility base class for the component installer script.
 *
 * Extends {@see \Joomla\CMS\Installer\InstallerScriptBase} on Joomla 4+ and
 * falls back to an empty class on earlier versions.
 */
if (class_exists('\\Joomla\\CMS\\Installer\\InstallerScriptBase')) {
    require_once JPATH_LIBRARIES . '/src/CMS/Installer/InstallerScriptBase.php';
    class com_payzenInstallerScriptBase extends \Joomla\CMS\Installer\InstallerScriptBase {}
} else {
    class com_payzenInstallerScriptBase {}
}

if (! class_exists('com_payzenInstallerScript')) {
    class com_payzenInstallerScript extends com_payzenInstallerScriptBase
    {
        static $plugin_features = array(
            'prodfaq' => true,
            'restrictmulti' => false,
            'shatwo' => true,

            'multi' => true
        );

        static $param_keys = array(
            'site_id',
            'key_test',
            'key_prod',
            'ctx_mode',
            'sign_algo',
            'platform_url',
            'language',
            'available_languages',
            'capture_delay',
            'validation_mode',
            'payment_cards',
            'redirect_enabled',
            'redirect_success_timeout',
            'redirect_success_message',
            'redirect_error_timeout',
            'redirect_error_message',
            'return_mode'
        );

       /**
        * Append a timestamped message to the specified log file.
        *
        * @param string $msg      The message to log
        * @param string $fileName The log file name (relative to Joomla log path)
        * @param string $level    The log level label (default 'INFO')
        *
        * @return void
        */
       static function log($msg, $fileName, $level = 'INFO')
        {
            $rawLogPath = Factory::getApplication()->get('log_path', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'logs');
            $logPath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $rawLogPath), DIRECTORY_SEPARATOR);
            $fLog = @fopen($logPath. DIRECTORY_SEPARATOR . $fileName, 'a');

            $date = date('Y-m-d H:i:s', time());

            if ($fLog) {
                fwrite($fLog, "$date - $level : $msg\n");
                fclose($fLog);
            }
        }

        /**
         * Returns the database driver using the DI container (Joomla 4+/5/6),
         * with a fallback to Factory::getDbo() for older environments.
         *
         * @suppress PhanDeprecatedFunction
         */
        private function getDb()
        {
            try {
                return Factory::getContainer()->get('DatabaseDriver');
            } catch (\Throwable $e) {
                return Factory::getDbo(); // @phpstan-ignore-line
            }
        }

        /**
         * Recursively copy a directory and all its contents to a destination path.
         *
         * @param string $src Source directory path
         * @param string $dst Destination directory path (created if absent)
         *
         * @return boolean True on success, false if the source directory does not exist
         */
        private function copyDirectory($src, $dst)
        {
            if (! is_dir($src)) {
                return false;
            }

            if (! is_dir($dst)) {
                mkdir($dst, 0755, true);
            }

            foreach (new \FilesystemIterator($src) as $item) {
                $destItem = $dst . DIRECTORY_SEPARATOR . $item->getFilename();
                if ($item->isDir()) {
                    $this->copyDirectory($item->getPathname(), $destItem);
                } else {
                    copy($item->getPathname(), $destItem);
                }
            }

            return true;
        }

        /**
         * Recursively delete a directory and all its contents.
         *
         * Silently skips the operation when the path does not exist or is not a directory.
         *
         * @param string $dir Path to the directory to delete
         *
         * @return void
         */
        private function deleteDirectory($dir)
        {
            if (! is_dir($dir)) {
                return;
            }

            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($items as $item) {
                if ($item->isDir()) {
                    @rmdir($item->getRealPath());
                } else {
                    @unlink($item->getRealPath());
                }
            }

            @rmdir($dir);
        }

        /**
         * Install a plugin by copying files and directly inserting/updating #__extensions.
         * Does NOT use Installer::getInstance() to avoid corrupting the component install state.
         */
        private function installPluginManually($sourceDir, $element, $group = 'hikashoppayment')
        {
            $app = Factory::getApplication();

            $targetDir = JPATH_PLUGINS . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $element;

            if (! $this->copyDirectory($sourceDir, $targetDir)) {
                $app->enqueueMessage('[payzen] Failed to copy plugin files: ' . $element, 'error');

                return false;
            }

            $manifestPath = $targetDir . DIRECTORY_SEPARATOR . $element . '.xml';
            if (! is_file($manifestPath)) {
                $app->enqueueMessage('[payzen] Plugin manifest not found: ' . $manifestPath, 'error');

                return false;
            }

            try {
                $db = $this->getDb();
                $xml = simplexml_load_file($manifestPath);

                $name = trim(strip_tags((string) $xml->name));
                $version = (string) $xml->version;
                $description = trim(strip_tags((string) $xml->description));
                $author = (string) $xml->author;
                $authorEmail = (string) $xml->authorEmail;
                $authorUrl = (string) $xml->authorUrl;
                $copyright = (string) $xml->copyright;
                $license = (string) $xml->license;
                $date = (string) $xml->creationDate;

                $manifestCache = json_encode(array(
                    'name' => $name,
                    'type' => 'plugin',
                    'creationDate'=> $date,
                    'author' => $author,
                    'copyright' => $copyright,
                    'authorEmail' => $authorEmail,
                    'authorUrl' => $authorUrl,
                    'version' => $version,
                    'description' => $description,
                    'group' => $group,
                    'namespace' => '',
                ));

                // Check for existing entry
                $query = $db->getQuery(true)
                    ->select($db->quoteName('extension_id'))
                    ->from($db->quoteName('#__extensions'))
                    ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
                    ->where($db->quoteName('folder') . ' = ' . $db->quote($group))
                    ->where($db->quoteName('element') . ' = ' . $db->quote($element));
                $db->setQuery($query);
                $extensionId = (int) $db->loadResult();

                if ($extensionId > 0) {
                    // Update existing row — preserve enabled state, just update metadata
                    $query = $db->getQuery(true)
                        ->update($db->quoteName('#__extensions'))
                        ->set($db->quoteName('name') . ' = ' . $db->quote($name))
                        ->set($db->quoteName('state') . ' = 0')
                        ->set($db->quoteName('package_id') . ' = 0')
                        ->set($db->quoteName('manifest_cache') . ' = ' . $db->quote($manifestCache))
                        ->where($db->quoteName('extension_id') . ' = ' . $extensionId);
                    $db->setQuery($query);
                    $db->execute();
                } else {
                    // Insert new row — build columns dynamically to handle schema differences
                    // between Joomla versions (e.g. 'locked' added in Joomla 4.0).
                    $columns = array('name', 'type', 'folder', 'element', 'client_id',
                        'enabled', 'access', 'protected', 'manifest_cache', 'params',
                        'custom_data', 'state', 'package_id');
                    $values = array(
                        $db->quote($name), $db->quote('plugin'), $db->quote($group),
                        $db->quote($element), '0', '0', '1', '0',
                        $db->quote($manifestCache), $db->quote('{}'), $db->quote(''), '0', '0'
                    );

                    // Add 'locked' only if the column exists (Joomla 4.0+)
                    try {
                        $cols = $db->getTableColumns('#__extensions');
                        if (isset($cols['locked'])) {
                            $columns[] = 'locked';
                            $values[]  = '0';
                        }
                    } catch (\Throwable $ignored) {}

                    $query = $db->getQuery(true)
                        ->insert($db->quoteName('#__extensions'))
                        ->columns(array_map(array($db, 'quoteName'), $columns))
                        ->values(implode(',', $values));
                    $db->setQuery($query);
                    $db->execute();
                }
            } catch (\Throwable $e) {
                $app->enqueueMessage('[payzen] installPluginManually DB error (' . $element . '): ' . $e->getMessage(), 'error');
                return false;
            }

            return true;
        }

        /**
         * Remove child plugins: deletes files and #__extensions rows.
         * Does NOT use Installer::uninstall() to avoid corrupting the component uninstall state.
         */
        private function removeChildPlugins()
        {
            try {
                $db = $this->getDb();
                $elements = array('payzen', 'payzenmulti');
                $root = JPATH_PLUGINS . DIRECTORY_SEPARATOR . 'hikashoppayment';

                foreach ($elements as $element) {
                    $dir = $root . DIRECTORY_SEPARATOR . $element;
                    if (is_dir($dir)) {
                        $this->deleteDirectory($dir);
                    }
                }

                $query = $db->getQuery(true)
                    ->delete($db->quoteName('#__extensions'))
                    ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
                    ->where($db->quoteName('element') . ' IN (' . implode(',', array_map(array($db, 'quote'), $elements)) . ')');
                $db->setQuery($query);
                $db->execute();
            } catch (\Throwable $e) {
                Factory::getApplication()->enqueueMessage('[payzen] removeChildPlugins failed: ' . $e->getMessage(), 'warning');
            }
        }

        // -----------------------------------------------------------------------
        // Joomla installer callbacks
        // -----------------------------------------------------------------------

        /**
         * Called before any installer action.
         *
         * On install/update: purges existing child plugin files and database rows so that
         * postflight always performs a clean installation, and removes any leftover plugin
         * subdirectories from the component admin folder.
         * On uninstall: does nothing and returns immediately.
         *
         * @param string           $route   Which action is happening (install|uninstall|discover_install|update)
         * @param \JAdapterInstance $adapter The object responsible for running this script
         *
         * @return boolean True on success
         */
        function preflight($route, $adapter)
        {
            if ($route === 'uninstall') {
                return true;
            }

            // Before install/update: purge existing child plugin files and DB rows so that
            // postflight always does a clean install (avoids Discover tab pollution).
            $this->removeChildPlugins();

            // Remove any leftover plugin subdirs that old versions may have copied into
            // the component admin folder (they would show up in Discover).
            $componentAdminPath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_payzen';

            foreach (array('plg_hikashoppayment_payzen', 'plg_hikashoppayment_payzenmulti') as $leftover) {
                $leftoverPath = $componentAdminPath . DIRECTORY_SEPARATOR . $leftover;
                if (is_dir($leftoverPath)) {
                    $this->deleteDirectory($leftoverPath);
                }
            }

            return true;
        }

        /**
         * Called after the component files have been installed.
         *
         * @return boolean True on success
         */
        function install()
        {
            return true;
        }

        /**
         * Called after any installer action.
         *
         * On install/update: copies child plugin directories from the package source and
         * registers each plugin in the Joomla extension table via {@see installPluginManually()}.
         * Skips on uninstall and discover_install routes.
         *
         * @param string            $route   Which action is happening (install|uninstall|discover_install|update)
         * @param \JAdapterInstance  $adapter The object responsible for running this script
         *
         * @return boolean True on success
         */
        function postflight($route, $adapter)
        {
            if ($route === 'uninstall' || $route === 'discover_install') {
                return true;
            }

            $sourcePath = $adapter->getParent()->getPath('source');

            // Install standard payment plugin (manual copy + DB, no Installer singleton).
            foreach (array('plg_hikashoppayment_payzen') as $dirName) {
                $pluginPath = $sourcePath . DIRECTORY_SEPARATOR . $dirName;
                if (is_dir($pluginPath)) {
                    // Element name = directory name without 'plg_hikashoppayment_'
                    $element = str_replace('plg_hikashoppayment_', '', $dirName);
                    $this->installPluginManually($pluginPath, $element);
                    break;
                }
            }

            // Install multi/installments plugin if enabled.
            if (self::$plugin_features['multi']) {
                foreach (array('plg_hikashoppayment_payzenmulti') as $dirName) {
                    $pluginPath = $sourcePath . DIRECTORY_SEPARATOR . $dirName;
                    if (is_dir($pluginPath)) {
                        $element = str_replace('plg_hikashoppayment_', '', $dirName);
                        $this->installPluginManually($pluginPath, $element);
                        break;
                    }
                }
            }

            return true;
        }

        /**
         * Called when the component is being uninstalled.
         *
         * Removes all child payment plugin files and their database rows before
         * the component itself is removed.
         *
         * @param \JAdapterInstance $adapter The object responsible for running this script
         *
         * @return boolean True on success
         */
        function uninstall($adapter)
        {
            // Remove child plugins cleanly before component files are removed.
            $this->removeChildPlugins();

            return true;
        }
    }
}
