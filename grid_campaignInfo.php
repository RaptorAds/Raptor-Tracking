<?php require "login/loginheader.php"; ?>
<html>
<head>
	<meta charset="utf-8">
	<title>RaptorAds Report (Campaign Info)</title>
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="/css/grid.css" rel="stylesheet" media="screen">
	<link rel="stylesheet" href="/css/style.css" >
	<link rel="stylesheet" href="/css/menu.css?v=0.3" >
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://admin.raptorads.com/js/dateformat.js"></script>
	<script src="/js/index.js"></script>
</head>
<body>
<?php
define('SERVER_ROOT', '/phpGrid_Lite');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) ."/phpGrid_Lite/conf.php");
date_default_timezone_set("Asia/Bangkok"); 

##############################

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
$sql = "delete from temp_campaignStat_sum; ";
$updateDB = "";

$sql = $sql."
				insert into temp_campaignStat_sum (campaignName, visits, clicks, `conversions`)
				select
					 campaignName
					 , visits
					 , clicks
					 , conversions
				from
					campaignStat
			";

## Set Filter
if ($_GET){
	$sql = $sql . " WHERE STR_TO_DATE(concat(year, '-' ,lpad(month, 2, '0'), '-' , day), '%Y-%m-%d %h:%i:%s %p') between STR_TO_DATE('".$_GET['from']."','%Y-%m-%d %h:%i:%s %p') and STR_TO_DATE('".$_GET['to']." 11:59:59 PM','%Y-%m-%d %h:%i:%s %p') and campaignName like '%".$_GET['campaign']."%'
	";
} else {
	$year = date('Y');
	$month = $str = ltrim(date('m'), '0');
	$day =  $str = ltrim(date('d'), '0');
	#$day = date('d');
	$sql = $sql . " WHERE Year = '".$year."' and Month = '".$month."' and Day = '".$day. "' ";
}

$sql = $sql . "GROUP BY campaignName ;";
$conn->multi_query($sql);
$conn->close();	

// create grid
$grid = new C_DataGrid("select
							campaignName as 'Campaign Name'
							, visits
							, clicks
							, conversions
							, round((clicks/visits*100), 2) as CTR
                            , round((conversions/clicks*100), 2) as CR
                            , round((conversions/visits*100), 2) as CV
						from
							temp_campaignStat_sum
						", "Campaign Name", "conversion");

$grid -> enable_export('EXCEL');
$grid -> enable_resize(true);
$grid -> enable_search(true);
$grid -> enable_advanced_search(true);
$grid -> set_dimension(700, 350);
$grid -> set_col_width("visits", 50);
$grid -> set_col_width("clicks", 60);
$grid -> set_col_width("conversions", 60);
$grid -> set_col_width("CTR", 40);
$grid -> set_col_width("CR", 30);
$grid -> set_col_width("CV", 30);
$grid -> set_col_width("Campaign Name", 300);

$grid -> set_grid_property(array("footerrow"=>true));
$loadComplete = <<<LOADCOMPLETE
function ()
{
var visits = $('#conversion').jqGrid('getCol', 'visits', false, 'sum');
var clicks = $('#conversion').jqGrid('getCol', 'clicks', false, 'sum');
var conversions = $('#conversion').jqGrid('getCol', 'conversions', false, 'sum');

$('#conversion').jqGrid('footerData', 'set', {'Campaign Name':'Grand Total','visits':visits, 'clicks':clicks,'conversions':conversions});

}
LOADCOMPLETE;
$grid -> add_event("jqGridLoadComplete", $loadComplete);
$grid -> set_pagesize(5000);

?>
<script>
$.fn.setPrevious = function (onlyBlank) {
	var date = new Date(), y = date.getFullYear(), m = date.getMonth();
	var firstDay = new Date(y, m, 1);
	var lastDay = new Date(y, m + 1, 0);
	formattedDateTime = date;
	formattedDateTime = DateFormat.format.date(formattedDateTime, "yyyy-MM-dd");
	
	if ( onlyBlank === true && $(this).val() ) {
	return this;
	}

	$(this).val(formattedDateTime);

	return this;
}
$.fn.setNow = function (onlyBlank) {
	var date = new Date(), y = date.getFullYear(), m = date.getMonth();
	var firstDay = new Date(y, m, 1);
	var lastDay = new Date(y, m + 1, 0);
	formattedDateTime = date;
	formattedDateTime = DateFormat.format.date(formattedDateTime, "yyyy-MM-dd");
	
	if ( onlyBlank === true && $(this).val() ) {
	return this;
	}

	$(this).val(formattedDateTime);

	return this;
}

$(function () {
    //Page Load
	
	//Get Reload Parameter
    var reload = location.search.split('reload=')[1];
	if (reload) {
		reload = reload.split('&')[0];
	}
	
	//Initialize 
	if (reload != "1"){
		$('input[id="dateFrom"]').setPrevious();
		$('input[id="dateTo"]').setNow();
	} else {
		$('input[id="dateFrom"]').value = '<?php if($_GET) echo $_GET['from']; ?>';
		$('input[id="dateTo"]').value = '<?php if($_GET) echo $_GET['to']; ?>';
	}
		
	$('input[id="dateFrom"]').datepicker({ dateFormat: 'yy-mm-dd' }).val();
	$('input[id="dateTo"]').datepicker({ dateFormat: 'yy-mm-dd' }).val();
});

function dateChange(){
	//Event on date changes
	var reload = "?reload=1";
	var from = "&from=" + $('input[id="dateFrom"]').val();
	var to = "&to=" + $('input[id="dateTo"]').val();
	var campaign = "&campaign=" + $('input[id="CampaignName"]').val()
	var parameters = reload + from + to + campaign;

	if(window.location.href.indexOf("admin.raptorads.com") > -1){
		window.location.href = window.location.href.replace( /[\?#].*|$/, parameters );
	}
	
}

</script>
<div id="wrapper">
  <div id="leftWrapper">
      <div id="listView" class="list">
        <li><a href="https://admin.raptorads.com/">Campaign Summary</a></li>
        <li><a href="https://admin.raptorads.com/grid_sum.php">Offer Summary</a></li>
		<li class="list-item-active"><a href="https://admin.raptorads.com/grid_campaignInfo.php">Visit/Convert Info</a></li>
		<li><a href="https://admin.raptorads.com/grid_countdupe.php">Count Duplicate</a></li>
        <li><a href="https://admin.raptorads.com/grid_subscription.php">Conversions</a></li>
        <li><a href="https://admin.raptorads.com/login/logout.php">Logout</a></li>
      </div>
    </div>
    <div id="rightWrapper">
      <div id="header"><a id="fullPage" href="#">|||</a></div>
      <div id="contentWrapper">
        <article id="showCase">
          <div class="article-header">RaptorAds Report</div>
		  <section style="width:95%;">
		  <fieldset id="SearchPanel">
				<legend>Search</legend>
				From:  <input id="dateFrom" type="text" name="dateFrom" onChange="dateChange()" size="10" 
				value='<?php if($_GET) echo $_GET['from']; ?>' />
				To:  <input id="dateTo" type="text" name="dateTo" onChange="dateChange()" size="10" 
				value='<?php if($_GET) echo $_GET['to']; ?>' />
				Campaign Name:  <input id="CampaignName" type="text" placeholder="Enter Campaign"
				value='<?php if($_GET) echo $_GET['campaign']; ?>'/>
				<button id="searchbtn" onclick="dateChange()"> Apply Search </button>
		  </fieldset>
		  </section>
		  <section>
			<?php $grid -> display(); ?>          
		  </section>
        </article>    
      </div>
    </div>
</div>
</body>