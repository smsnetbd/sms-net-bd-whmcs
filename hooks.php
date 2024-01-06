<?php


if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

require_once("sms_class.php");
$class = new SmsClass();
$hooks = $class->getHooks();

foreach ($hooks as $hook) {
    add_hook($hook['hook'], 1, $hook['function'], "");
}
