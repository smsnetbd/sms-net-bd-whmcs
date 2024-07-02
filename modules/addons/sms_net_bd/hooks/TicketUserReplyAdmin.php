<?php

$hook = array(
    'hook'           => 'TicketUserReply',
    'function'       => 'TicketUserReplyAdmin',
    'description' => 'When user has replied on the ticket.',
    'type'           => 'admin',
    'extra'          => '',
    'defaultmessage' => 'User has replied on the ticket #{ticketno} with the subject {subject}.',
    'variables' => '{subject},{ticketno},{company}'
);

if (!function_exists('TicketUserReplyAdmin')) {
    function TicketUserReplyAdmin($args)
    {
        $class    = new Functions();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if ($template['is_active'] == 0) {
            return null;
        }

        $settings = $class->getSettings();
        if (empty($settings['api_key'])) {
            logActivity('Hook Error: ' . 'No API Key Provided', 0);
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
                    logActivity('Hook Error: ' . 'Invalid phone number Provided', 0);
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
