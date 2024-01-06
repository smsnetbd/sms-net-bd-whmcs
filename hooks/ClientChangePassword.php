<?php

$hook = array(
    'hook'           => 'ClientChangePassword',
    'function'       => 'ClientChangePassword',
    'description' => 'After client change password',
    'type'           => 'client',
    'extra'          => '',
    'variables' => '{firstname},{lastname},{password},{company}',
    'defaultmessage' => 'Hi {firstname} {lastname},password has been changed successfully.',
);

if (!function_exists('ClientChangePassword')) {
    function ClientChangePassword($args)
    {

        $class    = new SmsClass();

        $template = $class->getTemplateDetails(__FUNCTION__);

        if ($template['is_active'] == 0) {

            return null;
        }

        $settings = $class->getSettings();

        if (empty($settings['api_key'])) {

            return null;
        }

        $result   = $class->getClientDetailsBy($args['userid']);
        $company_details = $class->getCompanyDetails();

        $num_rows = mysql_num_rows($result);

        if ($num_rows == 1) {

            $UserInformation       = mysql_fetch_assoc($result);

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);

            $replacefrom           = explode(",", $template['variables']);

            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $args['password'], $company_details['CompanyName']);

            $message               = str_replace($replacefrom, $replaceto, $template['content']);

            $class->setNumber($UserInformation['gsmnumber']);

            $class->setUserid($UserInformation['id']);

            $class->setMessage($message);

            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Client Password Change Sent Successfully', $UserInformation['id']);
            }
        }
    }
}



return $hook;
