<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once("sms_net_bd.class.php");

class Functions
{
    public $gsmnumber;
    public $message;
    public $userid;

    public function setNumber($gsmnumber)
    {
        $this->gsmnumber = $gsmnumber;
    }

    public function getNumber()
    {
        //return $this->gsmnumber;
        $number = $this->gsmnumber;
        //repace any non numeric character without +
        $number = preg_replace('/[^0-9+]/', '', $number);
        return $number;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    public function getUserid()
    {
        return $this->userid;
    }

    public function checkHooks($hooks = null)
    {
        if ($hooks == null) {
            $hooks = $this->getHooks();
        }

        $i = 0;

        foreach ($hooks as $hook) {
            $sql = "SELECT `id` FROM `sms_net_bd_templates` WHERE `name` = '" . $hook['function'] . "' AND `user_type` = '" . $hook['type'] . "' LIMIT 1";
            $result   = mysql_query($sql);
            $num_rows = mysql_num_rows($result);
            if ($num_rows == 0) {
                if ($hook['type']) {
                    $values = array(
                        "name"        => $hook['function'],
                        "user_type"        => $hook['type'],
                        "content"    => $hook['defaultmessage'],
                        "variables"   => $hook['variables'],
                        "extra_info"       => $hook['extra'],
                        "description" => $hook['description'],
                        "is_active"      => 0
                    );
                    insert_query("sms_net_bd_templates", $values);
                    $i++;
                }
            } else {
                $values = array(
                    "variables" => $hook['variables']
                );
                update_query("sms_net_bd_templates", $values, "name = '" . $hook['name'] . "'");
            }
        }

        return $i;
    }


    public function getHooks()
    {
        if ($handle = opendir(dirname(__FILE__) . '/hooks')) {
            while (false !== ($entry = readdir($handle))) {
                if (substr($entry, strlen($entry) - 4, strlen($entry)) == ".php") {
                    $file[] = require_once('hooks/' . $entry);
                }
            }
            closedir($handle);
        }
        return $file;
    }

    public function getTemplateDetails($template = null)
    {
        $where  = array("name" => $template);
        $result = select_query("sms_net_bd_templates", "*", $where);
        $data   = mysql_fetch_assoc($result);
        return $data;
    }

    public function getSettings()
    {
        $result = select_query("sms_net_bd_settings", "*", array('id' => 1));
        return mysql_fetch_array($result);
    }

    public function getClientDetailsBy($clientId)
    {
        $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `a`.`phonenumber` as `gsmnumber`, `a`.`country`
        FROM `tblclients` as `a` WHERE `a`.`id`  = '" . $clientId . "'
        LIMIT 1";
        return mysql_query($userSql);
    }

    public function getClientAndInvoiceDetailsBy($clientId)
    {
        $userSql = "
        SELECT a.total,a.duedate,b.id as userid,b.firstname,b.lastname,`b`.`country`,`b`.`phonenumber` as `gsmnumber` FROM `tblinvoices` as `a`
        JOIN tblclients as b ON b.id = a.userid
        WHERE a.id = '" . $clientId . "'
        LIMIT 1
    ";
        return mysql_query($userSql);
    }

    function changeDateFormat($date = null)
    {
        $dateformat = "%d.%m.%y";
        if (!$dateformat) {
            return $date;
        }
        $date       = explode("-", $date);
        $year       = $date[0];
        $month      = $date[1];
        $day        = $date[2];
        $dateformat = str_replace(array("%d", "%m", "%y"), array($day, $month, $year), $dateformat);
        return $dateformat;
    }

    public function getCompanyName()
    {
        $companySql = "SELECT `value` as `CompanyName` FROM `tblconfiguration` WHERE `setting` = 'CompanyName' LIMIT 1";
        $companySql = mysql_query($companySql);
        return mysql_fetch_assoc($companySql);
    }

    public function validatePhoneNumber($phoneNumber)
    {

        $trimmed = trim($phoneNumber);

        $pattern = '/^(?:\+?88)?01[3-9]\d{8}$/';

        if (preg_match($pattern, $trimmed)) {
            return $trimmed;
        } else {
            return false;
        }
    }

    public function send()
    {

        $settings = $this->getSettings();

        $send = new sms_net_bd($settings['api_key']);

        try {
            $resp = $send->sendSMS(
                $this->getMessage(),
                $this->getNumber(),
                $settings['sender_id']
            );
        } catch (\Throwable $th) {
            $resp = [
                "error" => 403,
                "msg" => $th->getMessage()
            ];
        }

        $table  = "sms_net_bd_messages";

        if (!isset($resp['data']['request_id'])) {
            $resp['data']['request_id'] = 0;
        }

        if (($resp['error']) == 0) {
            $status = "pending";
        } else {
            $status = "failed";
        }

        //get current Asia/Dhaka with php date time class
        $currentDate = new \DateTime('now', new \DateTimeZone('Asia/Dhaka'));

        $currentDate = $currentDate->format('Y-m-d H:i:s');

        $values = array(
            "sender_id" => $settings['sender_id'],
            "recipient" => $this->getNumber(),
            "message" => $this->message,
            "req_id" => $resp['data']['request_id'],
            "status" => $status,
            "error_details" => $resp['error'],
            "log_details" => $resp['msg'],
            "client_id" => $this->getUserid(),
            "created" => $currentDate
        );

        insert_query($table, $values);

        if ($resp['error'] == 0) {
            return true;
        }
        return false;
    }
}
