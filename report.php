<html>
<title>RaptorAds Tracking Report </title>
<?php
	$login = "95bfcd30-ffdd-48fa-8fe1-0ff3601defec:01KSv1g6IhtPe9zOWYKcgDQvp6KIGEnu9WEz";
	$service_url = 'https://api.voluum.com/auth/access/session';
	$data = array("accessId" => "95bfcd30-ffdd-48fa-8fe1-0ff3601defec", "accessKey" => "01KSv1g6IhtPe9zOWYKcgDQvp6KIGEnu9WEz");
	
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
	$currentdate = date("Y-m-d");
	$currentTime = strtotime($currentdate)-3600;
	$current = date('Y-m-d H:i:s',$currentTime);
	$tomorrow = new DateTime('tomorrow');
	
	$dateFrom = substr($current, 0,10);
	$timeFrom = substr($current, 11, 19);
	$current = $dateFrom.'T'.$timeFrom;
	
	$header = array();
    $header[] = "Cwauth-Token: " . $tok; 
    
	$sh = curl_init();
    curl_setopt($sh, CURLOPT_URL, "https://api.voluum.com/report/conversions?from=" .$current. "Z&to=" . $tomorrow->format('Y-m-d') . "T00:00:00Z&tz=Asia%2FBangkok&sort=postbackTimestamp&direction=desc&columns=postbackTimestamp&columns=visitTimestamp&columns=externalId&columns=clickId&columns=transactionId&columns=cost&columns=campaignId&columns=campaignName&columns=landerName&columns=landerId&columns=offerName&columns=offerId&columns=countryName&columns=countryCode&columns=trafficSourceName&columns=trafficSourceId&columns=affiliateNetworkName&columns=affiliateNetworkId&columns=deviceName&columns=os&columns=osVersion&columns=brand&columns=model&columns=browser&columns=browserVersion&columns=isp&columns=mobileCarrier&columns=connectionTypeName&columns=ip&columns=referrer&columns=customVariable1&columns=customVariable2&columns=customVariable3&columns=customVariable4&columns=customVariable5&columns=customVariable6&columns=customVariable7&columns=customVariable8&columns=customVariable9&columns=customVariable10&groupBy=conversion&offset=0&limit=1000&include=active&conversionTimeMode=VISIT");
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
	
	#echo "Token:  ". $tok . "..... </br> ";
    echo "<div style='display: none;'>Result: ". $results . "</div>";
	echo "<h1>RaptorAds Tracking Report</h1></br>...................</br></br>";
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
	
	## Loop Response ##
	#echo 	"<table><tr>"
	#		."<th>postbackTimestamp</th>"
	#		."<th>visitTimestamp</th>"
	#		."<th>externalId</th>"
	#		."<th>clickId</th>"
	#		."<th>transactionId</th>"
	#		."<th>cost</th>"
	#		."<th>campaignName</th>"
	#		."<th>countryName</th>"
	#		."</tr>";
	foreach($report_results['rows'] as $row){
		if (!empty($row['advertiserId'])){
			$sql = "INSERT INTO conversion (advertiserId,
									advertiserName,
									affiliateNetworkId,
									affiliateNetworkName,
									appendClickIdToOfferUrl,
									applicationBundle,
									applicationId,
									applicationName,
									brand,
									browser,
									browserVersion,
									campaignCountry,
									campaignId,
									campaignName,
									campaignNamePostfix,
									campaignUrl,
									city,
									clickId,
									clickIdArgument,
									clickRedirectType,
									clientId,
									connectionType,
									connectionTypeName,
									conversionActionId,
									conversionActionName,
									conversionType,
									conversions,
									cost,
									costArgument,
									costModel,
									countryCode,
									countryName,
									cpa,
									cpc,
									cpm,
									device,
									deviceName,
									externalCampaignId,
									externalId,
									flowId,
									flowName,
									ip,
									isp,
									landerCountry,
									landerId,
									landerName,
									landerUrl,
									mobileCarrier,
									model,
									numberOfOffers,
									offerCountry,
									offerId,
									offerName,
									offerUrl,
									onlyWhitelistedPostbackIps,
									os,
									osVersion,
									payout,
									pixelUrl,
									postbackTimestamp,
									postbackUrl,
									profit,
									publisherId,
									publisherName,
									referrer,
									referrerDomain,
									region,
									registrationHour,
									revenue,
									roi,
									siteId,
									trafficSourceId,
									trafficSourceName,
									transactionId,
									visitTimestamp
									) SELECT
										'".$row['advertiserId']."',
										'".$row['advertiserName']."',
										'".$row['affiliateNetworkId']."',
										'".$row['affiliateNetworkName']."',
										'".$row['appendClickIdToOfferUrl']."',
										'".$row['applicationBundle']."',
										'".$row['applicationId']."',
										'".$row['applicationName']."',
										'".$row['brand']."',
										'".$row['browser']."',
										'".$row['browserVersion']."',
										'".$row['campaignCountry']."',
										'".$row['campaignId']."',
										'".$row['campaignName']."',
										'".$row['campaignNamePostfix']."',
										'".$row['campaignUrl']."',
										'".$row['city']."',
										'".$row['clickId']."',
										'".$row['clickIdArgument']."',
										'".$row['clickRedirectType']."',
										'".$row['clientId']."',
										'".$row['connectionType']."',
										'".$row['connectionTypeName']."',
										'".$row['conversionActionId']."',
										'".$row['conversionActionName']."',
										'".$row['conversionType']."',
										'".$row['conversions']."',
										'".$row['cost']."',
										'".$row['costArgument']."',
										'".$row['costModel']."',
										'".$row['countryCode']."',
										'".$row['countryName']."',
										'".$row['cpa']."',
										'".$row['cpc']."',
										'".$row['cpm']."',
										'".$row['device']."',
										'".$row['deviceName']."',
										'".$row['externalCampaignId']."',
										'".$row['externalId']."',
										'".$row['flowId']."',
										'".$row['flowName']."',
										'".$row['ip']."',
										'".$row['isp']."',
										'".$row['landerCountry']."',
										'".$row['landerId']."',
										'".$row['landerName']."',
										'".$row['landerUrl']."',
										'".$row['mobileCarrier']."',
										'".$row['model']."',
										'".$row['numberOfOffers']."',
										'".$row['offerCountry']."',
										'".$row['offerId']."',
										'".$row['offerName']."',
										'".$row['offerUrl']."',
										'".$row['onlyWhitelistedPostbackIps']."',
										'".$row['os']."',
										'".$row['osVersion']."',
										'".$row['payout']."',
										'".$row['pixelUrl']."',
										'".$row['postbackTimestamp']."',
										'".$row['postbackUrl']."',
										'".$row['profit']."',
										'".$row['customVariable2']."',
										'".$row['publisherName']."',
										'".$row['referrer']."',
										'".$row['referrerDomain']."',
										'".$row['region']."',
										'".$row['registrationHour']."',
										'".$row['revenue']."',
										'".$row['roi']."',
										'".$row['siteId']."',
										'".$row['trafficSourceId']."',
										'".$row['trafficSourceName']."',
										'".$row['transactionId']."',
										'".$row['visitTimestamp']."'
										FROM conversion
			WHERE NOT EXISTS (
				SELECT * FROM conversion WHERE 
										clickId = '".$row['clickId']."'
			) LIMIT 1;";
			#echo "<tr>";
			#echo "<td>" . $row['postbackTimestamp'] . "  </td>";
			#echo "<td>" .$row['visitTimestamp'] . "  </td>";
			#echo "<td>" .$row['externalId'] . "  </td>";
			#echo "<td>" .$row['clickId'] . "  </td>";
			#echo "<td>" .$row['transactionId'] . "  </td>";
			#echo "<td>" .$row['cost'] . "  </td>";
			#echo "<td>" .$row['campaignName'] . "  </td>";
			#echo "<td>" .$row['countryName'] . "  </td>";
			#echo "</tr>";
			if ($conn->multi_query($sql) === TRUE) {
				$updateDB = "New records created successfully </br>";
			} else {
				$updateDB .= "Error: " . $sql . "</br>" . $conn->error;
			}
		}
	}
	#echo "</table>";
	## End Loop ##
	
	echo $updateDB;
	
	#Update 20170328 - Add Update Summary Table Query
	$sql = "";
	$sql = "RESET QUERY CACHE;
			FLUSH QUERY CACHE;
			insert into conversion_campaign_sum (campaignName, mobileCarrier, `year`, `month`, `day`, cost, revenue, profit, totalsub)
			select
				 campaignName
				, mobileCarrier
				, `year`
				, `month`
				, `day`
				, Cost
				, Revenue
				, Profit
				, TotalSub
			from
				conversion_campaignsum
			ON DUPLICATE KEY UPDATE cost = VALUES(cost), revenue = VALUES(revenue), profit = VALUES(profit), totalSub = VALUES(totalSub);
			";
	if ($conn->multi_query($sql) === TRUE) {
		$updateDB = "Campaign Summary has been updated </br>";
	} else {
		$updateDB .= "Error: " . $sql . "</br>" . $conn->error;
	}

	echo $updateDB;
	
	#Update 20170329 - Add update sum offer table
	$conn2 = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	$sql = "";
	$updateDB = "";
	$sql = "";
	$sql = "
			insert into conversion_offer_sum (offerName, mobileCarrier, `year`, `month`, `day`, cost, revenue, profit, Total)
			select
				 offerName
				, mobileCarrier
				, `year`
				, `month`
				, `day`
				, Cost
				, Revenue
				, Profit
				, Total
			from
				conversion_sum
			ON DUPLICATE KEY UPDATE cost = VALUES(cost), revenue = VALUES(revenue), profit = VALUES(profit), Total = VALUES(Total);
			";
			
	if ($conn2->multi_query($sql) === TRUE) {
		$updateDB = "Offer Summary has been updated </br>";
	} else {
		$updateDB .= "Error: " . $sql . "</br>" . $conn2->error;
	}

	echo $updateDB;
	
	#Close all connections
	$conn->close();	
	$conn2->close();
    curl_close ($sh);
	curl_close ($curl);
	
	#testing
	#echo var_dump($report_results);

?>
