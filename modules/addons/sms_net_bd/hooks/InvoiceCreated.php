<?php
$hook = array(
    'hook'           => 'InvoiceCreated',
    'function'       => 'InvoiceCreated',
    'description' => 'After Invoice Creation',
    'type'           => 'client',
    'extra'          => '',
    'defaultmessage' => 'Hello {firstname} {lastname}, Your invoice with id {invoiceid} has been generated.Total amount is  {total}.The last day of payment is {duedate}.Kindly pay your bill before due date to use services without interruption.',
    'variables' => '{firstname},{lastname},{date},{duedate},{total},{invoiceid},{company}'
);
if (!function_exists('InvoiceCreated')) {
    function InvoiceCreated($args)
    {
        $class    = new Functions();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if ($template['is_active'] == 0) {
            return null;
        }
        $settings = $class->getSettings();
        if (empty($settings['api_key'])) {
            logActivity('sms.net.bd - InvoiceCreated :  ' . 'No API Key Provided', 0);
            return null;
        }

        $result   = $class->getClientAndInvoiceDetailsBy($args['invoiceid']);
        $company_details = $class->getCompanyName();

        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);

            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                logActivity('sms.net.bd - InvoiceCreated :  ' . 'Invalid phone number Provided', 0);
                return null;
            }

            $template['variables'] = str_replace(" ", "", $template['variables']);
            $replacefrom     = explode(",", $template['variables']);
            $replaceto = array(
                $UserInformation['firstname'], $UserInformation['lastname'], $class->changeDateFormat($UserInformation['date']),
                $class->changeDateFormat($UserInformation['duedate']), $UserInformation['total'], $args['invoiceid'], $company_details['CompanyName']
            );
            $message         = str_replace($replacefrom, $replaceto, $template['content']);
            $class->setNumber($UserInformation['gsmnumber']);
            $class->setMessage($message);
            $class->setUserid($UserInformation['userid']);
            $status = $class->send();

            if ($status == 'success') {
                logActivity('SMS Notification of Invoice Creation Sent Successfully', $UserInformation['userid']);
            }
        }
    }
}
return $hook;
