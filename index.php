<?php
/**
 * User: Nicu Neculache
 * Date: 13.05.2019
 * Time: 19:26
 */

define('ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('CORE', ROOT . 'php' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR);
define('MODEL', ROOT . 'php' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR);
define('VIEW', ROOT . 'php' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR);
define('CONTROLLER', ROOT . 'php' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR);

$modules = [ROOT, CORE, MODEL, VIEW, CONTROLLER];
set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $modules));
spl_autoload_register('spl_autoload',false);

new Application;
