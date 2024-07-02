<?php
$hook = array(
	'hook'           => 'ClientEdit',
	'function'       => 'ClientEditClientarea',
	'description'    => 'After Client Edit',
	'type'           => 'client',
	'extra'          => '',
	'defaultmessage' => 'Dear {firstname} {lastname},your profile has been updated.',
	'variables' => '{firstname},{lastname},{company}'
);

if (!function_exists('ClientEditClientarea')) {

	function ClientEditClientarea($args)
	{

		$message = urlencode('Dear {firstname} {lastname},your profile has been updated.');

		$from = urlencode('WHMCS');

		$class    = new Functions();
		$template = $class->getTemplateDetails(__FUNCTION__);
		if ($template['is_active'] == 0) {
			return null;
		}

		$company_details = $class->getCompanyName();

		if ($args['olddata']['phonenumber'] != $args['phonenumber']) {
			//Set User
			$class->setUserid($args['userid']);
			$client_query = $class->getClientDetailsBy($class->userid);
			$client       = mysql_fetch_array($client_query);

			if (!$class->validatePhoneNumber($client['gsmnumber'])) {
				logActivity('Hook Error: ' . 'Invalid phone number Provided', 0);
				return null;
			}

			$message      = str_replace(['{firstname}', '{lastname}'], [$client['firstname'], $client['lastname'], $company_details['CompanyName']], $template['content']);
			$class->setNumber($client['gsmnumber']);
			$class->setMessage($message);
			$status = $class->send();

			if ($status == 'success') {
				logActivity('SMS Notification of Client Phone Number Change Sent Successfully', $args['userid']);
			}
		}
	}
}
return $hook;
