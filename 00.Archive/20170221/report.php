<html>
<head>
<title>RaptorAds Tracking Report </title>
<?php
#============== Retrieve Token ==========================================================#
function getToken($login){
	$service_url = 'https://security.voluum.com/login';
	$curl = curl_init($service_url);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($curl, CURLOPT_USERPWD, $login);		//Your credentials goes here
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);	//IMP if the url has https and you don't want to verify source certificate

	$curl_response = curl_exec($curl);
	$response = json_decode($curl_response, true);

	# Set Token Receive from response
	$token = $response['token']; 
	
	# Close cURL / Return Value
	curl_close($curl);
	return $token;
}
#============== Retrieve Report ==========================================================#
function getReport(){
	$reportToken = getToken("support@raptorads.com:1qazxsw2");
	$header = array();	
	$header[] = "Cwauth-Token: " . $reporttoken;
	$current = date("Y-m-d");
	$tomorrow = new DateTime('tomorrow');
	$curl_report = curl_init();
	curl_setopt($curl_report, CURLOPT_URL, "https://portal.voluum.com/report?from=" .$current. "T00:00:00Z&to=" . $tomorrow->format('Y-m-d') . "T00:00:00Z&tz=America%2FNew_York&sort=profit&direction=desc&columns=offerName&columns=visits&columns=clicks&columns=conversions&columns=revenue&columns=cost&columns=profit&columns=cpv&columns=ctr&columns=cr&columns=cv&columns=roi&columns=epv&columns=epc&columns=ap&columns=errors&columns=affiliateNetworkName&groupBy=offer&offset=0&limit=100&include=active&filter1=campaign&filter1Value=d6d28ae2-5bb9-4ac8-8906-2323d1ad5b1c");
	curl_setopt($curl_report, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_report, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($curl_report, CURLOPT_HTTPHEADER, $header);
	curl_setopt($curl_report, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl_report, CURLOPT_SSL_VERIFYPEER, 0);

	$curl_reportresponse = curl_exec($curl_report);
	if (curl_errno($curl_report)) {
		# Set Error Message
		$reportresponse = 'Error:' . curl_error($curl_report);
	} else {
		# Set return receive from response
		$reportresponse = json_decode($curl_reportresponse, true);
	}
	
	# Close cURL / Return Value
	curl_close($curl_report);
	#return var_dump($reportresponse);
	#testing
	return ("https://portal.voluum.com/report?from=" .$current. "T00:00:00Z&to=" . $tomorrow->format('Y-m-d') . "T00:00:00Z&tz=America%2FNew_York&sort=profit&direction=desc&columns=offerName&columns=visits&columns=clicks&columns=conversions&columns=revenue&columns=cost&columns=profit&columns=cpv&columns=ctr&columns=cr&columns=cv&columns=roi&columns=epv&columns=epc&columns=ap&columns=errors&columns=affiliateNetworkName&groupBy=offer&offset=0&limit=100&include=active&filter1=campaign&filter1Value=d6d28ae2-5bb9-4ac8-8906-2323d1ad5b1c");
}
 
?>

</head>
<body>
<H1>RaptorAds Tracking Report</H1>
<!-- Response Token  -->
<?#php echo getToken("support@raptorads.com:1qazxsw2"); ?>

</br >

<!-- Response Report  -->
<?php echo getReport(); ?>
</body>
</html>