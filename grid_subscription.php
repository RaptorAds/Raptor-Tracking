<?php require "login/loginheader.php"; ?>
<html>
<head>
	<meta charset="UTF-8">
	<title>RaptorAds Report (Conversions)</title>
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
// create grid
$grid = new C_DataGrid("SELECT
						STR_TO_DATE(postbackTimestamp,'%Y-%m-%d %h:%i:%s %p') as postbackTimestamp
						,STR_TO_DATE(visitTimestamp,'%Y-%m-%d %h:%i:%s %p') as visitTimestamp
						,clickId
						,transactionId
						,externalId
						,campaignName as 'Campaign Name'
						,landerName
						,offerName
						,offerId
						,countryCode
						,trafficSourceName
						,affiliateNetworkName
						,device
						,os
						,osVersion
						,brand
						,model
						,browser
						,browserVersion
						,isp
						,mobileCarrier as 'Carrier'
						,connectionTypeName
						,ip
						,referrer
						,publisherId as 'Publisher ID'
						FROM conversion", "clickId", "conversion");

$grid -> enable_export('EXCEL');
$grid -> enable_resize(true);
$grid -> enable_search(true);
$grid -> enable_advanced_search(true);

if ($_GET){
	$grid -> set_query_filter("STR_TO_DATE(postbackTimestamp,'%Y-%m-%d %h:%i:%s %p') between STR_TO_DATE('".$_GET['from']."','%Y-%m-%d %h:%i:%s %p') and STR_TO_DATE('".$_GET['to']." 11:59:59 PM','%Y-%m-%d %h:%i:%s %p')");
} else {
	$grid -> set_query_filter("STR_TO_DATE(postbackTimestamp,'%Y-%m-%d %h:%i:%s %p') between STR_TO_DATE('".date('Y-m-d')."','%Y-%m-%d %h:%i:%s %p') and STR_TO_DATE('".date('Y-m-d')." 11:59:59 PM', '%Y-%m-%d %h:%i:%s %p')");
}

$grid -> set_sortname('postbackTimestamp', 'DESC');

#Set UI columns
$grid -> set_dimension(700, 350);


$grid -> set_col_width("visitTimestamp", 0.000001);
$grid -> set_col_width("clickId", 0.000001);
$grid -> set_col_width("externalId", 0.000001);
$grid -> set_col_width("landerName", 0.000001);
$grid -> set_col_width("trafficSourceName", 0.000001);
$grid -> set_col_width("affiliateNetworkName", 0.000001);
$grid -> set_col_width("device", 0.000001);
$grid -> set_col_width("os", 0.000001);
$grid -> set_col_width("osVersion", 0.000001);
$grid -> set_col_width("brand", 0.000001);
$grid -> set_col_width("model", 0.000001);
$grid -> set_col_width("browser", 0.000001);
$grid -> set_col_width("browserVersion", 0.000001);
$grid -> set_col_width("isp", 0.000001);
$grid -> set_col_width("connectionTypeName", 0.000001);
$grid -> set_col_width("Carrier", 90);
$grid -> set_col_width("referrer", 0.000001);
$grid -> set_col_width("countryCode", 0.000001);
$grid -> set_col_width("offerId", 0.000001);
$grid -> set_col_width("transactionId", 0.000001);
$grid -> set_col_width("offerName", 0.000001);
$grid -> set_col_width("ip", 0.000001);
$grid -> set_col_width("postbackTimestamp", 140);
$grid -> set_col_width("Campaign Name", 360);
$grid -> set_col_width("Publisher ID", 100);
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
	var parameters = reload + from + to;

	if(window.location.href.indexOf("admin.raptorads.com") > -1){
		window.location.href = window.location.href.replace( /[\?#].*|$/, parameters );
	}
	
}
function searchGrid(){
	phpGrid_conversion.setGridParam({
	postData: {
	filters:'{"groupOp":" '+ $("#op").val() +' ","rules":['+
	'{"field":"Carrier","op":"cn","data":"' + $("#mobileCarrier").val() +'"}'+
	//',{"field":"clickId","op":"cn","data":"'+ $("#clickId").val() +'"}'+
	',{"field":"Publisher ID","op":"cn","data":"'+ $("#publisherId").val() +'"}]}'
	},
	"search":true,
	page:1
	}).trigger("reloadGrid");
}
</script>
<div id="wrapper">
  <div id="leftWrapper">
      <div id="listView" class="list">
        <li><a href="https://admin.raptorads.com/">Campaign Summary</a></li>
        <li><a href="https://admin.raptorads.com/grid_sum.php">Offer Summary</a></li>
		<li><a href="https://admin.raptorads.com/grid_campaignInfo.php">Visit/Convert Info</a></li>
		<li><a href="https://admin.raptorads.com/grid_countdupe.php">Count Duplicate</a></li>
        <li class="list-item-active"><a href="https://admin.raptorads.com/grid_subscription.php">Conversions</a></li>
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
				Carrier
				<select id="mobileCarrier" onchange="searchGrid()">
				  <option value="">All</option>
				  <option value="AIS">AIS</option>
				  <option value="DTAC">DTAC</option>
				  <option value="True Corporation">True</option>
				</select>
				<select id="op">
					<option value="AND">AND</option>
					<option value="OR">OR</option>
				</select>
				Publisher ID:  <input id="publisherId" type="text" placeholder="Enter Publisher ID" onblur="searchGrid()" />
				<button id="searchbtn" onclick="searchGrid()"> Apply Search </button>
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
</html>
