<?php
$hook = array(
    'hook'           => 'ClientAdd',
    'function'       => 'ClientAddAdmin',
    'description' => 'When client is added.',
    'type'           => 'admin',
    'extra'          => '',
    'defaultmessage' => 'New customer has been added to the website.',
    'variables' => ',{company}'
);

if (!function_exists('ClientAddAdmin')) {
    function ClientAddAdmin($args)
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
        $admin_numbers = explode(",", $template['admin_numbers']);

        $company_details = $class->getCompanyDetails();

        $template['variables'] = str_replace(" ", "", $template['variables']);
        $replacefrom           = explode(",", $template['variables']);
        $replaceto = array($company_details['CompanyName']);
        $template['content']   = str_replace($replacefrom, $replaceto, $template['content']);

        foreach ($admin_numbers as $gsm) {
            if (!empty($gsm)) {
                $class->setNumber(trim($gsm));
                $class->setUserid(0);
                $class->setMessage($template['content']);
                $class->send();
            }
        }
    }
}
return $hook;
