<?php require "login/loginheader.php"; ?>
<html>
<head>
	<meta charset="utf-8">
	<title>RaptorAds Report (Duplicate)</title>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="/css/grid.css" rel="stylesheet" media="screen">
	<link rel="stylesheet" href="/css/style.css" >
	<link rel="stylesheet" href="/css/menu.css?v=0.3" >
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://www.raptorads.com/js/dateformat.js"></script>
	<script src="/js/index.js"></script>
</head>
<body>
<?php
define('SERVER_ROOT', '/phpGrid_Lite');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) ."/phpGrid_Lite/conf.php");
date_default_timezone_set("Asia/Bangkok"); 

##############################
// create grid
$grid = new C_DataGrid("select
							transactionId as 'Transaction ID'
							, Year
							, Month
							, Day
							, TotalCount as 'Total Count'
						from
							conversion_duplicate
						", "Transaction ID", "conversion");

$grid -> enable_export('EXCEL');
$grid -> enable_resize(true);
$grid -> enable_search(true);
$grid -> enable_advanced_search(true);
$grid -> set_dimension(700, 350);
$grid -> set_col_width("Year", 40);
$grid -> set_col_width("Month", 40);
$grid -> set_col_width("Day", 40);
$grid -> set_col_width("Transaction ID", 350);

## Set Filter
if ($_GET){
	$grid -> set_query_filter("STR_TO_DATE(concat(year, '-' ,lpad(month, 2, '0'), '-' , day), '%Y-%m-%d %h:%i:%s %p') between STR_TO_DATE('".$_GET['from']."','%Y-%m-%d %h:%i:%s %p') and STR_TO_DATE('".$_GET['to']." 11:59:59 PM','%Y-%m-%d %h:%i:%s %p')");
} else {
	$year = date('Y');
	$month = $str = ltrim(date('m'), '0');
	$day = date('d');
	$grid -> set_query_filter("Year = '".$year."' and Month = '".$month."' and Day = '".$day. "'");
}

$grid -> set_grid_property(array("footerrow"=>true));

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

</script>
<div id="wrapper">
  <div id="leftWrapper">
      <div id="listView" class="list">
        <li><a href="https://admin.raptorads.com/">Campaign Summary</a></li>
        <li><a href="https://admin.raptorads.com/grid_sum.php">Offer Summary</a></li>
		<li><a href="https://admin.raptorads.com/grid_campaignInfo.php">Visit/Convert Info</a></li>
		<li class="list-item-active"><a href="https://admin.raptorads.com/grid_countdupe.php">Count Duplicate</a></li>
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