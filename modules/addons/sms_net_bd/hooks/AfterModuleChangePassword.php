<?php

$hook = array(
    'hook'           => 'AfterModuleChangePassword',
    'function'       => 'AfterModuleChangePassword',
    'description' => 'After module password changed',
    'type'           => 'client',
    'extra'          => '',
    'defaultmessage' => 'Hi {firstname} {lastname},password for the {domain} has been changed successfully.Here are the details- Username: {username} Password: {password}.',
    'variables' => '{firstname},{lastname},{domain},{username},{password},{company}'
);

if (!function_exists('AfterModuleChangePassword')) {
    function AfterModuleChangePassword($args)
    {
        $type = $args['params']['producttype'];
        if ($type == "hostingaccount") {
            $class    = new Functions();
            $template = $class->getTemplateDetails(__FUNCTION__);
            if ($template['is_active'] == 0) {
                return null;
            }
            $settings = $class->getSettings();
            if (empty($settings['api_key'])) {
                logActivity('sms.net.bd - module :  ' . 'No API Key Provided', 0);
                return null;
            }
        } else {
            return null;
        }
        $result   = $class->getClientDetailsBy($args['params']['clientsdetails']['userid']);
        $company_details = $class->getCompanyName();

        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                logActivity('sms.net.bd - module :  ' . 'Invalid phone number Provided', 0);
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom           = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $args['params']['domain'], $args['params']['username'], $args['params']['password'], $company_details['CompanyName']);
            $message                = str_replace($replacefrom, $replaceto, $template['content']);
            $class->setNumber($UserInformation['gsmnumber']);
            $class->setUserid($args['params']['clientsdetails']['userid']);
            $class->setMessage($message);
            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Module Password Change Sent Successfully', $args['params']['clientsdetails']['userid']);
            }
        }
    }
}
return $hook;
