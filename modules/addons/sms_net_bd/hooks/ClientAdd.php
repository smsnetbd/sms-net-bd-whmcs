<?php

$hook = array(
    'hook'           => 'ClientAdd',
    'function'       => 'ClientAddClientArea',
    'description' => 'After Client Registration',
    'type'           => 'client',
    'extra'          => '',
    'defaultmessage' => 'Hi {firstname}{lastname}, Thank you for registering with us.',
    'variables' => '{firstname},{lastname},{email},{password},{company}'
);

if (!function_exists('ClientAddClientArea')) {

    function ClientAddClientArea($args)
    {
        $class    = new Functions();

        $template = $class->getTemplateDetails(__FUNCTION__);

        if ($template['is_active'] == 0) {

            return null;
        }

        $settings = $class->getSettings();

        if (empty($settings['api_key'])) {
            logActivity('sms.net.bd - ClientAddClientArea :  ' . 'No API Key Provided', 0);
            return null;
        }

        $result   = $class->getClientDetailsBy($args['userid']);

        $company_details = $class->getCompanyName();


        $num_rows = mysql_num_rows($result);

        if ($num_rows == 1) {
            $UserInformation       = mysql_fetch_assoc($result);

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                logActivity('sms.net.bd - ClientAddClientArea :  ' . 'Invalid phone number Provided', 0);
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);

            $replacefrom           = explode(",", $template['variables']);

            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $args['email'], $args['password'], $company_details['CompanyName']);

            $message               = str_replace($replacefrom, $replaceto, $template['content']);

            $class->setNumber($UserInformation['gsmnumber']);

            $class->setMessage($message);

            $class->setUserid($args['userid']);

            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Client Registration Sent Successfully', $args['userid']);
            }
        }
    }
}



return $hook;
