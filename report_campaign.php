<html>
<title>RaptorAds Tracking Report </title>
<?php
	$login = "33f90ed3-9d81-48b6-9b31-d5676ce7b6ec:2qkgdoiK7Ulo2ioaZN47ZqQbEdtKIUG6ljRY";
	$service_url = 'https://api.voluum.com/auth/access/session';
	$data = array("accessId" => "33f90ed3-9d81-48b6-9b31-d5676ce7b6ec", "accessKey" => "2qkgdoiK7Ulo2ioaZN47ZqQbEdtKIUG6ljRY");
	
	$curl_post_data = json_encode($data); 
	
	$curl = curl_init($service_url);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($curl, CURLOPT_USERPWD, $login);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_HTTPHEADER,array(
		'Content-Type: application/json; charset=utf-8',
		'Accept: application/json',
	));	

	$curl_response = curl_exec($curl);
	$response = json_decode($curl_response, true);

	# Set Token Receive from response
	$tok = $response['token']; 
	
	# Close cURL / Return Value
	date_default_timezone_set("Asia/Bangkok");
	$currentdate = date("Y-m-d", strtotime("- 1 day"));
	$currentTime = strtotime($currentdate);
	$current = date('Y-m-d H:i:s',$currentTime);
	$tomorrow = date('Y-m-d H:i:s', strtotime("- 0 day"));
	$dateFrom = substr($current, 0,10);
	$timeFrom = substr($current, 11, 19);
	$current = $dateFrom.'T'.$timeFrom;
	$tomorrow = substr($tomorrow, 0,10).'T'.'00:00:00';
	
	$header = array();
    $header[] = "Cwauth-Token: " . $tok; 
    
	$sh = curl_init();
    curl_setopt($sh, CURLOPT_URL, "https://panel-api.voluum.com/report?from=" .$current. "Z&to=" . $tomorrow . "Z&tz=Asia%2FBangkok&sort=visits&direction=desc&columns=campaignName&columns=status&columns=bid&columns=visits&columns=clicks&columns=conversions&columns=revenue&columns=cost&columns=profit&columns=cpv&columns=ctr&columns=cr&columns=cv&columns=roi&columns=epv&columns=epc&columns=ap&columns=errors&columns=trafficSourceName&groupBy=campaign&offset=0&limit=500&include=ACTIVE&conversionTimeMode=VISIT");
    curl_setopt($sh, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($sh, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($sh, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($sh, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($sh, CURLOPT_HTTPHEADER, $header);

    $results = curl_exec($sh);
	$report_results = json_decode($results, true);
	
    if (curl_errno($sh)) {
        echo 'Error:' . curl_error($sh);
    }
	
    echo "<div style='display: none;'>Result: ". $results . "</div>";
	echo "<h1>RaptorAds Campaign Report API</h1></br>...................</br></br>";
	
	## Test String ##
	
	## Insert Response ##
	$servername = "localhost";
	$username = "natsoon";
	$password = "IloveUMass#316";
	$dbname = "raptortracking";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	$sql = "";
	$updateDB = "";
	
	foreach($report_results['rows'] as $row){
		if (!empty($row['visits'])){
			$sql = "INSERT INTO campaignStat (`campaignName`,
											`visits`,
											`clicks`,
											`conversions`,
											`year`,
											`month`,
											`day`
											) VALUES (
											'".$row['campaignName']."',
											'".$row['visits']."',
											'".$row['clicks']."',
											'".$row['conversions']."',
											year('".$current."'),
											month('".$current."'),
											day('".$current."')
											)
			ON DUPLICATE KEY UPDATE visits = VALUES(visits), clicks = VALUES(clicks), conversions = VALUES(conversions);
			";

			if ($conn->multi_query($sql) === TRUE) {
				$updateDB = "New records created successfully </br>";
			} else {
				$updateDB .= "Error: " . $sql . "</br>" . $conn->error;
			}
		}
	}
	## End Loop ##
	
	echo $updateDB;
	
	#Close all connections
	$conn->close();	
    curl_close ($sh);
	curl_close ($curl);

?>
