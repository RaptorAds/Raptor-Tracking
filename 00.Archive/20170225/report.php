<html>
<title>RaptorAds Tracking Report </title>
<?php
	$login = "support@raptorads.com:1qazxsw2";
	$service_url = 'https://security.voluum.com/login';
	$curl = curl_init($service_url);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($curl, CURLOPT_USERPWD, $login);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);	

	$curl_response = curl_exec($curl);
	$response = json_decode($curl_response, true);

	# Set Token Receive from response
	$tok = $response['token']; 
	
	# Close cURL / Return Value

	$current = date("Y-m-d");
	$tomorrow = new DateTime('tomorrow');
	$header = array();
    $header[] = "Cwauth-Token: " . $tok; 
    
	$sh = curl_init();
    curl_setopt($sh, CURLOPT_URL, "https://portal.voluum.com/report?from=" .$current. "T00:00:00Z&to=" . $tomorrow->format('Y-m-d') . "T00:00:00Z&tz=America%2FNew_York&sort=profit&direction=desc&columns=offerName&columns=visits&columns=clicks&columns=conversions&columns=revenue&columns=cost&columns=profit&columns=cpv&columns=ctr&columns=cr&columns=cv&columns=roi&columns=epv&columns=epc&columns=ap&columns=errors&columns=affiliateNetworkName&groupBy=offer&offset=0&limit=100&include=active&filter1=campaign&filter1Value=d6d28ae2-5bb9-4ac8-8906-2323d1ad5b1c");
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
    echo "Result: ". $results;
	echo "<h1>RaptorAds Tracking Report</h1></br>...................</br></br>";
	## Loop Response ##
	
	echo 	"<table><tr>"
			."<th>offerName</th>"
			."<th>visits</th>"
			."<th>clicks</th>"
			."<th>conversions</th>"
			."<th>revenue</th>"
			."<th>cost</th>"
			."<th>profit</th>"
			."<th>cpv</th>"
			."<th>ctr</th>"
			."<th>cr</th>"
			."<th>cv</th>"
			."<th>roi</th>"
			."<th>epv</th>"
			."<th>epc</th>"
			."<th>ap</th>"
			."<th>errors</th>"
			."<th>affiliateNetworkName</th>"
			."</tr>";
	foreach($report_results['rows'] as $row){
		if (!empty($row['offerName'])){
			echo "<tr>";
			echo "<td>" . $row['offerName'] . "  </td>";
			echo "<td>" .$row['visits'] . "  </td>";
			echo "<td>" .$row['clicks'] . "  </td>";
			echo "<td>" .$row['conversions'] . "  </td>";
			echo "<td>" .$row['revenue'] . "  </td>";
			echo "<td>" .$row['cost'] . "  </td>";
			echo "<td>" .$row['profit'] . "  </td>";
			echo "<td>" .$row['cpv'] . "  </td>";
			echo "<td>" .$row['ctr'] . "  </td>";
			echo "<td>" .$row['cr'] . "  </td>";
			echo "<td>" .$row['cv'] . "  </td>";
			echo "<td>" .$row['roi'] . "  </td>";
			echo "<td>" .$row['epv'] . "  </td>";
			echo "<td>" .$row['epc'] . "  </td>";
			echo "<td>" .$row['ap'] . "  </td>";
			echo "<td>" .$row['errors'] . "  </td>";
			echo "<td>" .$row['affiliateNetworkName'] . "  </td>";
			echo "</tr>";
		}
	}
	echo "</table>";
	
	## End Loop ##
	
    curl_close ($sh);
	curl_close ($curl);

?>
