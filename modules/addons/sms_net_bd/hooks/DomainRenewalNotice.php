<?php
$hook = array(
    'hook'           => 'DailyCronJob',
    'function'       => 'DomainRenewalNotice',
    'description' => 'Domain Renewal Notice before {x} days.',
    'type'           => 'client',
    'extra'          => '15',
    'defaultmessage' => 'Hi {firstname} {lastname},your domain- {domain} will expire in {x} days i.e. on {expirydate}.Kindly visit site to renew it.Thank You!.',
    'variables' => '{firstname},{lastname},{domain},{expirydate},{x},{company}'
);

if (!function_exists('DomainRenewalNotice')) {
    function DomainRenewalNotice($args)
    {
        $class    = new Functions();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if ($template['is_active'] == 0) {
            return null;
        }
        $settings = $class->getSettings();
        $company_details = $class->getCompanyName();
        if (empty($settings['api_key'])) {
            return null;
        }
        $extra     = $template['extra'];
        $sqlDomain = "SELECT  `userid` ,  `domain` ,  `expirydate`
           FROM  `tbldomains`
           WHERE  `status` =  'Active'";
        $resultDomain = mysql_query($sqlDomain);
        while ($data = mysql_fetch_array($resultDomain)) {
            $tarih     = explode("-", $data['expirydate']);
            $yesterday = mktime(0, 0, 0, $tarih[1], $tarih[2] - $extra, $tarih[0]);
            $today     = date("Y-m-d");
            if (date('Y-m-d', $yesterday) == $today) {
                $result   = $class->getClientDetailsBy($data['userid']);
                $num_rows = mysql_num_rows($result);
                if ($num_rows == 1) {
                    $UserInformation       = mysql_fetch_assoc($result);
                    if (!$class->validatePhoneNumber($UserInformation['gsmnumber'])) {
                        return null;
                    }
                    $template['variables'] = str_replace(" ", "", $template['variables']);
                    $replacefrom           = explode(",", $template['variables']);
                    $replaceto = array($UserInformation['firstname'], $UserInformation['lastname'], $data['domain'], $data['expirydate'], $extra, $company_details['CompanyName']);
                    $message               = str_replace($replacefrom, $replaceto, $template['content']);
                    $class->setNumber($UserInformation['gsmnumber']);
                    $class->setMessage($message);
                    $class->setUserid($UserInformation['userid']);
                    $status = $class->send();

                    if ($status == 'success') {
                        logActivity('SMS Notification of Domain Renewal Notice Sent Successfully', $UserInformation['userid']);
                    }
                }
            }
        }
    }
}
return $hook;
