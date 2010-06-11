<?php
class AutoloadPackage
{
    /**
     * Where to get pyrus.phar from, if it does not exist on the filesystem.
     *
     * @var string
     */
    static $pyrus_uri = 'http://svn.php.net/viewvc/pear2/Pyrus/trunk/pyrus.phar?view=co';

    /**
     * Where pyrus.phar is located.
     *
     * @var string
     */
    static $pyrus_file = '/tmp/pyrus.phar';

    /**
     * The directory to store the autoload package registry in.
     *
     * @var string
     */
    static $autoload_registry = '/tmp/pear2';

    /**
     * Autoloader for a package.
     *
     * @param string $class The class to attempt to install and load.
     *
     * @return void
     */
    static function autoload($class)
    {
        $file = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $class) . '.php';
        if (!file_exists(self::$autoload_registry . '/php/' . $file)) {
            $package = self::classToPackage($class);
            self::getPyrus();
            if (file_exists('phar://' . self::$pyrus_file . '/PEAR2_Pyrus-2.0.0a1/php/' . $file)) {
                include 'phar://' . self::$pyrus_file . '/PEAR2_Pyrus-2.0.0a1/php/' . $file;
                return;
            }
            self::installPackage($package);
        }
        include self::$autoload_registry . '/php/' . $file;
    }

    /**
     * Install a PEAR package.
     *
     * @param string $package Name of the package to install. PEAR2_Templates_Savant
     *
     * @return void
     */
    static function installPackage($package)
    {
        $config = \PEAR2\Pyrus\Config::singleton(self::$autoload_registry);
        $config->preferred_state = 'alpha';
        $p = new \PEAR2\Pyrus\Package($package);
        try {
            \PEAR2\Pyrus\Installer::begin();
            \PEAR2\Pyrus\Installer::prepare($p);
            \PEAR2\Pyrus\Installer::commit();
        } catch (Exception $e) {
            \PEAR2\Pyrus\Installer::rollback();
            echo $e;
        }
    }

    /**
     * Grab the pyrus file if necessary.
     *
     * @return void
     */
    static function getPyrus()
    {
        if (!file_exists(self::$pyrus_file)) {
            $pyrus = file_get_contents(self::$pyrus_uri);
            file_put_contents(self::$pyrus_file, $pyrus);
        }
    }

    /**
     * Guess a package name from a class name.
     *
     * @param string $class Class name, eg: PEAR2\Templates\Savant\Main
     *
     * @return string The best guess at a package name. eg: PEAR2_Templates_Savant
     */
    static function classToPackage($class)
    {
        return str_replace(array('\\','_Main'), array('_',''), $class);
    }
}

spl_autoload_register('AutoloadPackage::autoload');
