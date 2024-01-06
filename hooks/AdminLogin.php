<?php
$hook = array(
    'hook'           => 'AdminLogin',
    'function'       => 'AdminLoginAdmin',
    'type'           => 'admin',
    'extra'          => '',
    'defaultmessage' => 'A user with the username {username} has entered the admin panel.',
    'variables' => '{username},{company}'
);

if (!function_exists('AdminLoginAdmin')) {
    function AdminLoginAdmin($args)
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
        $replaceto = array($args['username'], $company_details['CompanyName']);
        $message               = str_replace($replacefrom, $replaceto, $template['content']);

        $class->setNumber($template['admin_numbers']);
        $class->setUserid(0);
        $class->setMessage($message);
        $class->send();
    }
}

return $hook;
