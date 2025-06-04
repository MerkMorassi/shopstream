<!doctype html>
<head>
<title>Live Dynamic Product Stream</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="robots" content="no index,no follow" />
<meta http-Equiv="Cache-Control" Content="no-cache">
<meta http-Equiv="Pragma" Content="no-cache">
<meta http-Equiv="Expires" Content="0">
<link rel="stylesheet" type="text/css" href="css/style-3.css" media="screen" />
</head>
<body>

<div id="content-full">
<div id="video" style="height:518px;">
<!-- If Live, live stream displayed; if not show graphic or replay of prerecorded -->
<!-- <iframe src="https://embed.bambuser.com/channel/creativefiredigitalmedia" width="920" height="518" allowfullscreen frameborder="0">Your browser does not support iframes.</iframe> -->
</div>				
</div>			

<div id="content-full" style="height:80px;margin-bottom:10px;background-color:#333;">
<article class="articlecontent" style="margin-top:10px;text-align:center;">
<h2 style="font-size: 60px;">Real-Time Retail Demo</h2><br>
<p style="color:#FFF";><em>Live Stream With Dynamic Product Data: Countdown w/ Disappearing Deals; Real-Time QTY Updates</em></p><br>
</article>
</div>
<?php
$conn=mysqli_connect('localhost','root','Dingbat427X_$','shopstream');
$query="SELECT pDisplay,pInStock,pTime,pTimeStart,pTimeEnd FROM products";
$result=mysqli_query($conn,$query);
$output=mysqli_fetch_assoc($result);
$display = $output['pDisplay'];
$stock = $output['pInStock'];
$timestart = $output['pTimeStart'];
$timeend = $output['pTimeEnd'];
// date_default_timezone_set("America/Phoenix");
$now = date("Y-m-d h:i:s");
// if ($display == "1" && $stock > 0) {
if ($display == "1" && $stock > 1 && strtotime($timestart) < strtotime($now) && strtotime($now) < strtotime($timeend)) {
	echo "<iframe id='myIframe' src='iframe.php' width='960' height='1000' style='border:none;'></iframe>";
} else {
	echo "<div id='content-full' style='height:80px;margin-bottom:10px;background-color:#333;'><article class='articlecontent' style='margin-top:10px;text-align:center;'><h2 style='font-size: 32px;'>Deal Expired or Item Sold Out!</h2><p style='color:#FFF'><em>We'll keep checking for updates automatically</em></p></article></div>";
}
?>
</div>
</body>
</html>