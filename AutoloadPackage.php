<?php
class AutoloadPackage
{
    static $pyrus_uri = 'http://svn.php.net/viewvc/pear2/Pyrus/trunk/pyrus.phar?view=co';
    
    static $pyrus_file = '/tmp/pyrus.phar';
    
    static $autoload_registry = '/tmp/testpyrus';
    
    static function autoload($class)
    {
        $package = self::classToPackage($class);
        self::getPyrus();
        $class = str_replace('_', '\\', $class);
        $class = implode('/', explode('\\', $class));
        if (file_exists('phar://' . self::$pyrus_file . '/PEAR2_Pyrus-2.0.0a1/php/' . $class . '.php')) {
            include 'phar://' . self::$pyrus_file . '/PEAR2_Pyrus-2.0.0a1/php/' . $class . '.php';
            return;
        }
        self::installPackage($package);
        include self::$autoload_registry . '/php/' . $class . '.php';
    }
    
    static function installPackage($package)
    {
        $config = \pear2\Pyrus\Config::singleton(self::$autoload_registry);
        $config->preferred_state = 'alpha';

        $p = new \pear2\Pyrus\Package($package);
        try {
            \pear2\Pyrus\Installer::begin();
            \pear2\Pyrus\Installer::prepare($p);
            \pear2\Pyrus\Installer::commit();
        } catch (Exception $e) {
            \pear2\Pyrus\Installer::rollback();
            echo $e;
        }
    }
    
    static function getPyrus()
    {
        if (!file_exists(self::$pyrus_file)) {
            $pyrus = file_get_contents(self::$pyrus_uri);
            file_put_contents(self::$pyrus_file, $pyrus);
        }
    }
    
    static function classToPackage($class)
    {
        return str_replace(array('\\','_Main'), array('_',''), $class);
    }
}

spl_autoload_register('AutoloadPackage::autoload');

?>