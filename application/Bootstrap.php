<?php

require_once 'Zend/Loader.php'; 
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Ns_');
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{


}

