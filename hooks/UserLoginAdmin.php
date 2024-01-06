<?php
$hook = array(
    'hook'           => 'UserLogin',
    'function'       => 'UserLoginAdmin',
    'description' => 'When client login.',
    'type'           => 'admin',
    'extra'          => '',
    'defaultmessage' => 'Client with the name- {firstname} {lastname} made entrance to the site.',
    'variables' => '{firstname},{lastname},{company}'
);

if (!function_exists('UserLoginAdmin')) {
    function UserLoginAdmin($args)
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
        $result   = $class->getClientDetailsBy($args['userid']);
        $company_details = $class->getCompanyDetails();
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation       = mysql_fetch_assoc($result);
            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom           = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $company_details['CompanyName']);
            $message               = str_replace($replacefrom, $replaceto, $template['content']);
            foreach ($admin_numbers as $gsm) {
                if (!empty($gsm)) {
                    $class->setNumber(trim($gsm));
                    $class->setUserid(0);
                    $class->setMessage($message);
                }
                $class->send();
            }
        }
    }
}

return $hook;
