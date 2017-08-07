<html>
<title>RaptorAds Tracking Report </title>
<?php
    echo "<div style='display: none;'>Result: ". $results . "</div>";
	echo "<h1>RaptorAds Duplicate Report</h1></br>...................</br></br>";
	
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
	$updateDB = "";
	$sql = "
		insert into duplicateLog (transactionId, campaignName, `year`, `month`, `day`, postbackTimestamp)
		select 
			a.transactionId
			, b.campaignName
			, a.Year
			, a.Month
			, a.Day
			, b.postbackTimestamp 
		from 
			conversion_duplicate a 
			left join conversion b on a.transactionId = b.transactionId 
		where 
			b.created >= curdate() - INTERVAL 1 DAY 
		ON DUPLICATE KEY UPDATE campaignName = VALUES(campaignName);
		";
	
	if ($conn->multi_query($sql) === TRUE) {
		$updateDB = "Duplicate Log has been created</br>";
	} else {
		$updateDB .= "Error: " . $sql . "</br>" . $conn->error;
	}
	
	echo $updateDB;
	
	#Close all connections
	$conn->close();	

?>
