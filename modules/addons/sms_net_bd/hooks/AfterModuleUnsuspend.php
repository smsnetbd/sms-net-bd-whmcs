<?php
$hook = array(
    'hook'           => 'AfterModuleUnsuspend',
    'function'       => 'AfterModuleUnsuspend',
    'description' => 'After module unsuspend',
    'type'           => 'client',
    'extra'          => '',
    'defaultmessage' => 'Hello! The services {product} have now been made active.',
    'variables' => '{firstname},{lastname},{product},{domain},{company}'
);

if (!function_exists('AfterModuleUnsuspend')) {
    function AfterModuleUnsuspend($args)
    {
     
        $class    = new Functions();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if ($template['is_active'] == 0) {
            return null;
        }

        $settings = $class->getSettings();

        if (empty($settings['api_key'])) {
            logActivity('sms.net.bd - AfterModuleUnsuspend :  ' . 'No API Key Provided', 0);
            return null;
        }

        $result = $class->getClientDetailsBy($args['params']['clientsdetails']['userid']);

        $company_details = $class->getCompanyName();
        
        $num_rows = mysql_num_rows($result);

        if ($num_rows == 1) {
            $UserInformation       = mysql_fetch_assoc($result);

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                logActivity('sms.net.bd - AfterModuleUnsuspend :  ' . 'Invalid phone number Provided', 0);
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom           = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $args['params']['model']['product']['name'], $args['params']['domain'], $company_details['CompanyName']);
            $message               = str_replace($replacefrom, $replaceto, $template['content']);
            $class->setNumber($UserInformation['gsmnumber']);
            $class->setUserid($args['params']['clientsdetails']['userid']);
            $class->setMessage($message);
            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Hosting Module Unsuspension Sent Successfully', $args['params']['clientsdetails']['userid']);
            }
        }
    }
}

return $hook;
