<?php
$hook = array(
    'hook'           => 'AfterModuleSuspend',
    'function'       => 'AfterModuleSuspend',
    'description' => 'After Module Suspension',
    'type'           => 'client',
    'extra'          => '',
    'defaultmessage' => 'Hi {firstname} {lastname},The service for your account associated with the domain {domain} has been paused.Kindly contact us for more details.',
    'variables' => '{firstname},{lastname},{domain},{company}'
);

if (!function_exists('AfterModuleSuspend')) {
    function AfterModuleSuspend($args)
    {
        $type = $args['params']['producttype'];
        if ($type == "hostingaccount") {
            $class    = new SmsClass();
            $template = $class->getTemplateDetails(__FUNCTION__);
            if ($template['is_active'] == 0) {
                return null;
            }
            $settings = $class->getSettings();
            if (empty($settings['api_key'])) {
                return null;
            }
        } else {
            return null;
        }
        $result   = $class->getClientDetailsBy($args['params']['clientsdetails']['userid']);
        $company_details = $class->getCompanyName();

        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation       = mysql_fetch_assoc($result);

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom           = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $args['params']['domain'], $company_details['CompanyName']);
            $message               = str_replace($replacefrom, $replaceto, $template['content']);
            $class->setNumber($UserInformation['gsmnumber']);
            $class->setUserid($args['params']['clientsdetails']['userid']);
            $class->setMessage($message);
            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Hosting Module Suspension Sent Successfully', $args['params']['clientsdetails']['userid']);
            }
        }
    }
}
return $hook;
