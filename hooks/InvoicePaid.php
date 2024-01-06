<?php
$hook = array(
    'hook'           => 'InvoicePaid',
    'function'       => 'InvoicePaid',
    'description' => 'Post Payment',
    'type'           => 'client',
    'extra'          => '',
    'defaultmessage' => 'Dear {firstname} {lastname},payment for invoice with id {invoiceid} is done! Thank you.',
    'variables' => '{firstname},{lastname},{date},{duedate},{datepaid},{total},{invoiceid},{company}'
);

if (!function_exists('InvoicePaid')) {
    function InvoicePaid($args)
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
        $result   = $class->getClientAndInvoiceDetailsBy($args['invoiceid']);

        $company_details = $class->getCompanyName();

        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation       = mysql_fetch_assoc($result);

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom           = explode(",", $template['variables']);
            $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $class->changeDateFormat($UserInformation['date']), $class->changeDateFormat($UserInformation['duedate']), $class->changeDateFormat($UserInformation['datepaid']), $UserInformation['total'], $args['invoiceid'], $company_details['CompanyName']);
            $message               = str_replace($replacefrom, $replaceto, $template['content']);
            $class->setNumber($UserInformation['gsmnumber']);
            $class->setMessage($message);
            $class->setUserid($UserInformation['userid']);
            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Invoice Payment Sent Successfully', $UserInformation['userid']);
            }
        }
    }
}

return $hook;
