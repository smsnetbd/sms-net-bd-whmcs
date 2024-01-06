<?php
$hook = array(
    'hook'           => 'AfterRegistrarRegistration',
    'function'       => 'AfterRegistrarRegistrationAdmin',
    'description' => 'When domain registered.',
    'type'           => 'admin',
    'extra'          => '',
    'defaultmessage' => 'New domain named {domain} have been registered.',
    'variables' => '{domain},{company}'
);

if (!function_exists('AfterRegistrarRegistrationAdmin')) {
    function AfterRegistrarRegistrationAdmin($args)
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

        $company_details = $class->getCompanyName();

        $admin_numbers              = explode(",", $template['admin_numbers']);
        $template['variables'] = str_replace(" ", "", $template['variables']);
        $replacefrom           = explode(",", $template['variables']);
        $replaceto = array($args['params']['sld'] . "." . $args['params']['tld'], $company_details['CompanyName']);
        $message               = str_replace($replacefrom, $replaceto, $template['content']);
        foreach ($admin_numbers as $gsm) {
            if (!empty($gsm)) {
                $class->setNumber(trim($gsm));
                $class->setUserid(0);
                $class->setMessage($message);
                $class->send();
            }
        }
    }
}
return $hook;
