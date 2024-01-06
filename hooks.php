<?php


if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

require_once("functions.php");
$class = new Functions();
$hooks = $class->getHooks();

foreach ($hooks as $hook) {
    add_hook($hook['hook'], 1, $hook['function'], "");
}
