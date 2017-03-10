<?php
date_default_timezone_set('UTC');
defined('DOCUMENT_ROOT') || define('DOCUMENT_ROOT', dirname(dirname(__FILE__)));
defined('APPLICATION_PATH') || define('APPLICATION_PATH', DOCUMENT_ROOT . '/test/');

spl_autoload_register(function($className) {
  $namespace = str_replace("\\", "/", __NAMESPACE__);
  $className = str_replace("\\", "/", $className);
  $class = APPLICATION_PATH . (empty($namespace) ? "" : $namespace . "/") . "{$className}.php";
  require_once($class);
});

require_once(APPLICATION_PATH.'/config/config.php');

use Classes\Input;
use Classes\DataParser;

try {
  $input = new Input();
  $parser = new DataParser($config, $input->getOptions());
  $parser->parseXml();
}

catch(\Exception $e) {
  echo $e->getMessage();
}

?>
