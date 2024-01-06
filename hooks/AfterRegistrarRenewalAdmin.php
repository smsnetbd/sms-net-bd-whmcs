<?php

$hook = array(
    'hook'           => 'AfterRegistrarRenewal',
    'function'       => 'AfterRegistrarRenewalAdmin',
    'description' => 'When domain is renewed.',
    'type'           => 'admin',
    'extra'          => '',
    'defaultmessage' => 'The domain name {domain} has been renewed.',
    'variables' => '{domain},{company}'
);

if (!function_exists('AfterRegistrarRenewalAdmin')) {
    function AfterRegistrarRenewalAdmin($args)
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

        if (empty($template['admin_numbers'])) {
            return null;
        }

        $company_details = $class->getCompanyName();


        $template['variables'] = str_replace(" ", "", $template['variables']);
        $replacefrom           = explode(",", $template['variables']);
        $replaceto = array($args['params']['sld'] . "." . $args['params']['tld'], $company_details['CompanyName']);
        $message               = str_replace($replacefrom, $replaceto, $template['content']);

        $class->setNumber($template['admin_numbers']);
        $class->setUserid(0);
        $class->setMessage($message);
        $class->send();
    }
}

return $hook;
