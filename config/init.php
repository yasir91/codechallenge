<?php
//Load configurations
require_once 'config.php';

//class autoload
function __autoload($class_name)
{
    require_once  'app/lib/' . $class_name . '.php';
}
