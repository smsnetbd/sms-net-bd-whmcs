<?php

/**
 * sms.net.bd
 *
 * @package    sms.net.bd
 * @version    1.0.0
 * @link       https://www.sms.net.bd
 * @since      File available since Release 1.0
 */

require_once("sms_class.php");


if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

//defien current path
define("SMS_NET_BD_PATH", dirname(__FILE__));

function sms_net_bd_config()
{
    $configarray = array(
        "name"        => "sms.net.bd",
        "description" => "WHMCS SMS Addon. You can see details from : https://www.sms.net.bd",
        "version"     => "1.0.0",
        "author"      => "sms.net.bd",
        "language"    => "english",
    );
    return $configarray;
}

function sms_net_bd_activate()
{

    $query = "CREATE TABLE IF NOT EXISTS `sms_net_bd_templates`(
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(50) NOT NULL,
        `user_type` ENUM('client', 'admin') NOT NULL,
        `admin_numbers` VARCHAR(255) NOT NULL,
        `content` TEXT NOT NULL,
        `variables` VARCHAR(500) NOT NULL,
        `is_active` TINYINT NOT NULL,
        `extra_info` VARCHAR(3) NOT NULL COMMENT 'to store domain renew before expiry days',
        `description` TEXT NULL,
        PRIMARY KEY (`id`)
    )";

    mysql_query($query);


    $query = "CREATE TABLE IF NOT EXISTS `sms_net_bd_settings`(
        `id` INT NOT NULL AUTO_INCREMENT,
        `api_key` VARCHAR(40) NOT NULL,
        `sender_id` VARCHAR(100) NOT NULL,
        `version` VARCHAR(6) NULL,
        PRIMARY KEY (`id`)
    )";

    mysql_query($query);


    $query = "CREATE TABLE IF NOT EXISTS `sms_net_bd_messages`(
        `id` INT NOT NULL AUTO_INCREMENT,
        `sender_id` VARCHAR(40) NOT NULL,
        `recipient` VARCHAR(15) NULL,
        `message` TEXT NULL,
        `req_id` BIGINT NOT NULL,
        `status` VARCHAR(10) NULL,
        `error_details` TEXT NULL,
        `log_details` TEXT NULL,
        `client_id` INT NULL,
        `created` DATETIME NOT NULL,
        PRIMARY KEY (`id`)
    )";

    mysql_query($query);


    $query = "CREATE TABLE IF NOT EXISTS `sms_net_bd_otp`(
        `id` INT NOT NULL AUTO_INCREMENT,
        `client_id` VARCHAR(11) NOT NULL,
        `phone_number` VARCHAR(20) NOT NULL,
        `status` TINYINT NULL,
        PRIMARY KEY (`id`)
    )";


    mysql_query($query);

    $query = "INSERT INTO `sms_net_bd_settings` (`id`, `api_key`, `sender_id`, `version`) VALUES
    (1, '', '', '1.0.0')";

    mysql_query($query);

    $function = new SmsClass();
    $function->checkHooks();

    return array('status' => 'success', 'description' => 'sms.net.bd successfully activated.');
}

function sms_net_bd_deactivate()
{
    $query = "DROP TABLE IF EXISTS `sms_net_bd_templates`";
    mysql_query($query);

    $query = "DROP TABLE IF EXISTS `sms_net_bd_settings`";
    mysql_query($query);

    $query = "DROP TABLE IF EXISTS `sms_net_bd_messages`";
    mysql_query($query);

    $query = "DROP TABLE IF EXISTS `sms_net_bd_otp`";
    mysql_query($query);

    return array('status' => 'success', 'description' => 'sms.net.bd successfully deactivated.');
}

function sms_net_bd_upgrade()
{
}

function sms_net_bd_output()
{

    function showAlert($message, $type = 'success')
    {
        echo '<div class="alert alert-' . $type . ' alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span></button>' . $message . '</div>';
    }

    function formatHookName($inputString)
    {

        $formattedString = preg_replace('/_admin$/', '', $inputString);
        // Remove any extra spaces at the beginning or end
        $formattedString = trim($formattedString);

        return $formattedString;
    }


    try {

        $tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';

        //include style
        include_once SMS_NET_BD_PATH . '/partials/style.php';

        //if tab not found
        if (!file_exists(SMS_NET_BD_PATH . '/tabs/' . $tab . '.php')) {
            throw new Exception("Tab not found");
        }

        include_once SMS_NET_BD_PATH . '/tabs/' . $tab . '.php';
    } catch (\Throwable $th) {
        echo "Error: " . $th->getMessage();
    }
}
