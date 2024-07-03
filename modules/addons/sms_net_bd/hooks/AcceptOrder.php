<?php
$hook = array(
    'hook'           => 'AcceptOrder',
    'function'       => 'AcceptOrderSMS',
    'description' => 'Post Order Acceptance',
    'type'           => 'client',
    'extra'          => '',
    'defaultmessage' => 'Dear {firstname} {lastname},Your order associated with the ID #{orderid} has been approved.',
    'variables' => '{firstname},{lastname},{orderid},{company}'
);

if (!function_exists('AcceptOrderSMS')) {
    function AcceptOrderSMS($args)
    {
        $class    = new Functions();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if ($template['is_active'] == 0) {
            return null;
        }
        $settings = $class->getSettings();
        $company_details = $class->getCompanyName();
        if (empty($settings['api_key'])) {
            logActivity('sms.net.bd - AcceptOrderSMS :  ' . 'No API Key Provided', 0);
            return null;
        }

        $order_id = $args['orderid'];
        $userSql  = "SELECT id, firstname, lastname, phonenumber as gsmnumber, country FROM `tblclients` WHERE id IN (SELECT userid FROM tblorders WHERE id = '$order_id')";

        $result   = mysql_query($userSql);
        $num_rows = mysql_num_rows($result);

        if ($num_rows == 1) {
            $UserInformation       = mysql_fetch_assoc($result);

            if ($UserInformation['country'] != 'BD') {
                logActivity('sms.net.bd - AcceptOrderSMS :  ' . 'SMS not sent, This client is not from Bangladesh', 0);
                return null;
            }

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                logActivity('sms.net.bd - AcceptOrderSMS :  ' . 'Invalid phone number Provided', 0);
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom           = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $args['orderid'], $company_details['CompanyName']);
            $message               = str_replace($replacefrom, $replaceto, $template['content']);
            $class->setNumber($UserInformation['gsmnumber']);
            $class->setUserid($UserInformation['id']);
            $class->setMessage($message);

            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Order Acceptance Sent Successfully', $UserInformation['id']);
            }
        }
    }
}

return $hook;
