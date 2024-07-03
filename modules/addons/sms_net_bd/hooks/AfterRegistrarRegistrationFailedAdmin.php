<?php
$hook = array(
    'hook'           => 'AfterRegistrarRegistrationFailed',
    'function'       => 'AfterRegistrarRegistrationFailedAdmin',
    'description' => 'When client login.',
    'type'           => 'admin',
    'extra'          => '',
    'defaultmessage' => 'An error occurred while recording the domain {domain}.',
    'variables' => '{domain},{company}'
);

if (!function_exists('AfterRegistrarRegistrationFailedAdmin')) {
    function AfterRegistrarRegistrationFailedAdmin($args)
    {
        $class    = new Functions();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if ($template['is_active'] == 0) {
            return null;
        }
        $settings = $class->getSettings();
        if (empty($settings['api_key'])) {
            logActivity('sms.net.bd - AfterRegistrarRegistrationFailedAdmin :  ' . 'No API Key Provided', 0);
            return null;
        }
        $company_details = $class->getCompanyName();

        $admin_numbers              = explode(",", $template['admin_numbers']);
        $template['variables'] = str_replace(" ", "", $template['variables']);
        $replacefrom           = explode(",", $template['variables']);
        $replaceto = array($args['params']['sld'] . "." . $args['params']['tld'], $company_details['CompanyName']);
        $message               = str_replace($replacefrom, $replaceto, $template['content']);
        foreach ($admin_numbers as $gsm) {
            if (!empty($gsm)) {

                if (!$class->validatePhoneNumber($gsm)) {
                    logActivity('sms.net.bd - AfterRegistrarRegistrationFailedAdmin :  ' . 'Invalid phone number Provided', 0);
                    continue;
                }

                $class->setNumber(trim($gsm));
                $class->setUserid(0);
                $class->setMessage($message);
                $class->send();
            }
        }
    }
}

return $hook;
