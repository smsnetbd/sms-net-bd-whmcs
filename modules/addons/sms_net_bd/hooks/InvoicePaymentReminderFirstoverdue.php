<?php
$hook = array(
    'hook'           => 'InvoicePaymentReminder',
    'function'       => 'InvoicePaymentReminderFirstoverdue',
    'description' => 'Invoice payment reminder for first overdue',
    'type'           => 'client',
    'extra'          => '',
    'defaultmessage' => 'Hi {firstname} {lastname},the payment associated with your account with date {duedate} is not done yet for invoice {invoiceid}. Kindly make the payment at the earliest to enjoy the services.',
    'variables' => '{firstname},{lastname},{date},{duedate},{total},{invoiceid},{company}'
);

if (!function_exists('InvoicePaymentReminderFirstoverdue')) {
    function InvoicePaymentReminderFirstoverdue($args)
    {
        if ($args['type'] == "firstoverdue") {
            $class    = new Functions();
            $template = $class->getTemplateDetails(__FUNCTION__);
            if ($template['is_active'] == 0) {
                return null;
            }
            $settings = $class->getSettings();
            if (empty($settings['api_key'])) {
                logActivity('sms.net.bd - module :  ' . 'No API Key Provided', 0);
                return null;
            }
        } else {
            return false;
        }
        $result   = $class->getClientAndInvoiceDetailsBy($args['invoiceid']);
        $company_details = $class->getCompanyName();
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation       = mysql_fetch_assoc($result);

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                logActivity('sms.net.bd - module :  ' . 'Invalid phone number Provided', 0);
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom           = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $class->changeDateFormat($UserInformation['date']), $class->changeDateFormat($UserInformation['duedate']), $UserInformation['total'], $args['invoiceid'], $company_details['CompanyName']);
            $message               = str_replace($replacefrom, $replaceto, $template['content']);
            $class->setNumber($UserInformation['gsmnumber']);
            $class->setMessage($message);
            $class->setUserid($UserInformation['userid']);
            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Invoice Payment Reminder of first overdue Sent Successfully', $UserInformation['userid']);
            }
        }
    }
}

return $hook;
