<?php
$hook = array(
    'hook'           => 'TicketAdminReply',
    'function'       => 'TicketAdminReply',
    'description' => 'After Reply By Admin',
    'type'           => 'client',
    'extra'          => '',
    'variables' => '{firstname},{lastname},{ticketsubject},{company}',
    'defaultmessage' => 'Dear {firstname} {lastname},{ticketsubject} has been responded by admin.',
);

if (!function_exists('TicketAdminReply')) {
    function TicketAdminReply($args)
    {
        $class    = new Functions();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if ($template['is_active'] == 0) {
            return null;
        }
        $settings = $class->getSettings();
        if (empty($settings['api_key'])) {
            logActivity('sms.net.bd - TicketAdminReply :  ' . 'No API Key Provided', 0);
            return null;
        }

        $company_details = $class->getCompanyName();

        $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `a`.`phonenumber` as `gsmnumber`, `a`.`country`
        FROM `tblclients` as `a`
        WHERE `a`.`id` IN (SELECT userid FROM tbltickets WHERE id = '" . $args['ticketid'] . "')
        LIMIT 1";

        $result   = mysql_query($userSql);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation       = mysql_fetch_assoc($result);

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                logActivity('sms.net.bd - TicketAdminReply :  ' . 'Invalid phone number Provided', 0);
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom           = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $args['subject'], $company_details['CompanyName']);
            $message               = str_replace($replacefrom, $replaceto, $template['content']);
            $class->setNumber($UserInformation['gsmnumber']);
            $class->setMessage($message);
            $class->setUserid($UserInformation['id']);
            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Ticket Reply Sent Successfully', $UserInformation['id']);
            }
        }
    }
}

return $hook;
