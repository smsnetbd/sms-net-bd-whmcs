<?php
$hook = array(
    'hook'           => 'AfterRegistrarRenewalFailed',
    'function'       => 'AfterRegistrarRenewalFailedAdmin',
    'description' => 'When domain registration failed.',
    'type'           => 'admin',
    'extra'          => '',
    'defaultmessage' => 'An error occurred while updating the domain {domain}.',
    'variables' => '{domain},{company}'
);

if (!function_exists('AfterRegistrarRenewalFailedAdmin')) {
    function AfterRegistrarRenewalFailedAdmin($args)
    {
        $class    = new Functions();
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

                if (!$class->validatePhoneNumber($gsm)) {
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
