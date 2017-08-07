<?php require "login/loginheader.php"; ?>
<html>
<head>
	<meta charset="utf-8">
	<title>RaptorAds Report (Campaign Summary)</title>
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="/css/grid.css" rel="stylesheet" media="screen">
	<link rel="stylesheet" href="/css/style.css" >
	<link rel="stylesheet" href="/css/menu.css?v=0.3" >
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://admin.raptorads.com/js/dateformat.js"></script>
	<script src="/js/index.js"></script>
</head>
<body>
<style>
.ui-widget-content 
{ 
	border: 1px solid #a6c9e2; 
	color: #222222; 
}
.footrow-ltr {
	background-color: #ffc1fa;
}
.footrow {
	background-color: #ffc1fa;
}
</style>
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
$sql = "delete from temp_conversion_campaign_sum; ";
$updateDB = "";

$sql = $sql."
				insert into temp_conversion_campaign_sum (campaignName, AIS, DTAC, `TRUE`, Others, Total, Cost, Revenue, Profit, Payout)
				select
					 campaignName
					 , Case when mobileCarrier = 'AIS' then sum(totalSub )else '0' end as 'AIS'
					 , Case when mobileCarrier = 'DTAC' then sum(totalSub)else '0' end as 'DTAC'
					 , Case when mobileCarrier like '%True%' then sum(totalSub) else '0' end as 'TRUE'
					 , Case when NULLIF(mobileCarrier, ' ') IS NULL or mobileCarrier in 
						('WIFI', 'Other', 'Vodafone', 'Wind', 'TIM') then sum(totalSub) else '0' end as 'Others'
					 , sum(totalsub) as Total
					 , sum(cost) as Cost
					 , sum(revenue) as Revenue
					 , sum(profit) as Profit
					 , CONVERT(sum(cost) / sum(totalsub), DECIMAL(4,2))as Payout
				from
					conversion_campaign_sum
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

$sql = $sql . "GROUP BY campaignName, mobileCarrier	;";
$conn->multi_query($sql);
$conn->close();	

// create grid
$grid = new C_DataGrid("select
                             campaignName as 'Campaign Name'
							 , AIS
							 , DTAC
							 , `TRUE`
							 , Others
							 , AIS + DTAC + `TRUE` + Others as Total
							 , Cost
							 , Revenue
							 , Profit
							 , Payout
						from
							temp_conversion_campaign_sum
							", "Campaign Name", "conversion");

$grid -> enable_export('EXCEL');
$grid -> set_col_hidden('rank', false);
$grid -> enable_resize(true);
$grid -> enable_search(true);
$grid -> enable_advanced_search(true);
$grid -> set_dimension(850, 350);
$grid -> set_col_width("Campaign Name", 420);
$grid -> set_col_width("AIS", 60);
$grid -> set_col_width("DTAC", 60);
$grid -> set_col_width("TRUE", 60);
$grid -> set_col_width("Others", 60);
$grid -> set_col_width("Total", 80);
$grid -> set_col_width("Cost", 80);
$grid -> set_col_width("Revenue", 80);
$grid -> set_col_width("Profit", 80);
$grid -> set_col_width("Payout", 80);
$grid -> set_sortname('Total', 'DESC');

$grid -> set_grid_property(array("footerrow"=>true));
$loadComplete = <<<LOADCOMPLETE
function ()
{
var AIS = $('#conversion').jqGrid('getCol', 'AIS', false, 'sum');
var DTAC = $('#conversion').jqGrid('getCol', 'DTAC', false, 'sum');
var TRUE = $('#conversion').jqGrid('getCol', 'TRUE', false, 'sum');
var Others = $('#conversion').jqGrid('getCol', 'Others', false, 'sum');
var Total = $('#conversion').jqGrid('getCol', 'Total', false, 'sum');
var totalCost = $('#conversion').jqGrid('getCol', 'Cost', false, 'sum');
var totalRev = $('#conversion').jqGrid('getCol', 'Revenue', false, 'sum');
var totalProfit = $('#conversion').jqGrid('getCol', 'Profit', false, 'sum');

$('#conversion').jqGrid('footerData', 'set', {'Campaign Name':'Grand Total','AIS':AIS, 'DTAC':DTAC,'TRUE':TRUE,'Others':Others,'Total':Total,'Cost':totalCost.toFixed(2) ,'Revenue': totalRev.toFixed(2) ,'Profit': totalProfit.toFixed(2)});

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

function searchGrid(){
	phpGrid_conversion.setGridParam({
	postData: {
	filters:'{"groupOp":" '+ $("#op").val() +' ","rules":['+
	'{"field":"Campaign Name","op":"cn","data":"'+ $("#CampaignName").val() +'"}]}'
	},
	"search":true,
	page:1
	}).trigger("reloadGrid");
} 
</script>

<div id="wrapper">
  <div id="leftWrapper">
      <div id="listView" class="list">
        <li class="list-item-active"><a href="https://admin.raptorads.com/">Campaign Summary</a></li>
        <li><a href="https://admin.raptorads.com/grid_sum.php">Offer Summary</a></li>
		<li><a href="https://admin.raptorads.com/grid_campaignInfo.php">Visit/Convert Info</a></li>
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