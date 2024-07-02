<?php
$hook = array(
    'hook'           => 'AfterRegistrarRegistration',
    'function'       => 'AfterRegistrarRegistration',
    'description' => 'After Domain Registration',
    'type'           => 'client',
    'extra'          => '',
    'defaultmessage' => 'Hi {firstname} {lastname},Entries in the name field for the domain name {domain} have been successfully made.',
    'variables' => '{firstname},{lastname},{domain},{company}'
);

if (!function_exists('AfterRegistrarRegistration')) {
    function AfterRegistrarRegistration($args)
    {
        $class    = new Functions();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if ($template['is_active'] == 0) {
            return null;
        }
        $settings = $class->getSettings();
        if (empty($settings['api_key'])) {
            logActivity('Hook Error: ' . 'No API Key Provided', 0);
            return null;
        }
        $result = $class->getClientDetailsBy($args['params']['userid']);
        $company_details = $class->getCompanyName();


        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation       = mysql_fetch_assoc($result);

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                logActivity('Hook Error: ' . 'Invalid phone number Provided', 0);
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom           = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $args['params']['sld'] . "." . $args['params']['tld'], $company_details['CompanyName']);
            $message               = str_replace($replacefrom, $replaceto, $template['content']);
            $class->setNumber($UserInformation['gsmnumber']);
            $class->setUserid($args['params']['userid']);
            $class->setMessage($message);
            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Domain Registration Sent Successfully', $args['params']['userid']);
            }
        }
    }
}
return $hook;
