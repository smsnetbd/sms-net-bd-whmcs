<?php
$hook = array(
    'hook'           => 'TicketClose',
    'function'       => 'TicketClose',
    'description' => 'Ticket Closure',
    'type'           => 'client',
    'extra'          => '',
    'defaultmessage' => 'Hello {firstname} {lastname},The ticket with the ticket number #{ticketno} has been successfully closed.In case of any issue,kindly contact us.',
    'variables' => '{firstname},{lastname},{ticketno},{company}',
);

if (!function_exists('TicketClose')) {
    function TicketClose($args)
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

        $userSql = "
        SELECT a.tid,b.id as userid,b.firstname,b.lastname,`b`.`country`,`b`.`phonenumber` as `gsmnumber` FROM `tbltickets` as `a`
        JOIN tblclients as b ON b.id = a.userid WHERE a.id = '" . $args['ticketid'] . "'
        LIMIT 1";

        $result   = mysql_query($userSql);
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation       = mysql_fetch_assoc($result);

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom           = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $UserInformation['tid'], $company_details['CompanyName']);
            $message               = str_replace($replacefrom, $replaceto, $template['content']);
            $class->setNumber($UserInformation['gsmnumber']);
            $class->setMessage($message);
            $class->setUserid($UserInformation['userid']);
            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Ticket Closure Sent Successfully', $UserInformation['userid']);
            }
        }
    }
}

return $hook;
