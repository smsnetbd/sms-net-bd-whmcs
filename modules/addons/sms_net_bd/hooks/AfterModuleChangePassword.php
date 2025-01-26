<?php
$hook = array(
    'hook'           => 'AfterModuleChangePassword',
    'function'       => 'AfterModuleChangePassword',
    'description'    => 'After module password changed',
    'type'           => 'client',
    'extra'          => '',
    'defaultmessage' => 'Hi {firstname} {lastname}, the password for {domain} has been changed. New password: {newpassword}.',
    'variables'      => '{firstname},{lastname},{domain},{username},{newpassword},{company}'
);

if (!function_exists('AfterModuleChangePassword')) {
    function AfterModuleChangePassword($args) {
        // Extract hook parameters
        $serviceId = $args['serviceid']; // From hook
        $newPassword = $args['newpassword']; // From hook

        // Fetch service details (domain, username, clientid) using serviceid
        $serviceQuery = mysql_query("SELECT domain, username, userid AS clientid FROM tblhosting WHERE id = '$serviceId'");
        if (mysql_num_rows($serviceQuery) == 0) {
            logActivity("sms.net.bd - AfterModuleChangePassword: Service ID $serviceId not found");
            return null;
        }
        $service = mysql_fetch_assoc($serviceQuery);

        // Use existing helper functions
        $class = new Functions();
        $result = $class->getClientDetailsBy($service['clientid']); // Pass clientid from service
        $company_details = $class->getCompanyName();

        // Validate client data
        $num_rows = mysql_num_rows($result);
        if ($num_rows == 1) {
            $UserInformation = mysql_fetch_assoc($result);

            // Validate phone number
            if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                logActivity('sms.net.bd - AfterModuleChangePassword: Invalid phone number');
                return null;
            }

            // Prepare template replacements
            $replacefrom = explode(",", str_replace(" ", "", $hook['variables']));
            $replaceto = array(
                $UserInformation['firstname'],
                $UserInformation['lastname'],
                $service['domain'],
                $service['username'],
                $newPassword, // Use newpassword from hook args
                $company_details['CompanyName']
            );

            // Build message
            $message = str_replace($replacefrom, $replaceto, $hook['defaultmessage']);

            // Send SMS
            $class->setNumber($UserInformation['gsmnumber']);
            $class->setMessage($message);
            $status = $class->send();

            if ($status == 'success') {
                logActivity("SMS Notification: Password changed for service $serviceId");
            }
        }
    }
}
return $hook;
