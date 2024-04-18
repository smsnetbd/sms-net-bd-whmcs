<?php
$hook = array(
    'hook'           => 'TicketOpenAdmin',
    'function'       => 'TicketOpenAdmin',
    'description' => 'When new ticket is created.',
    'type'           => 'admin',
    'extra'          => '',
    'defaultmessage' => 'A new ticket #{ticketno} with the subject {subject} has been created.',
    'variables' => '{subject},{ticketno},{company}'
);

if (!function_exists('TicketOpenAdmin')) {
    function TicketOpenAdmin($args)
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
        $replaceto = array($args['subject'], $args['ticketmask'], $company_details['CompanyName']);
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
