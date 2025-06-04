<?php
//=========================================
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property
//of Internet Business Solutions SL. Any use, reproduction, disclosure or copying
//of any kind without the express and written permission of Internet Business 
//Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
include "db_conn_open.php";
include "includes.php";
include "inc/incfunctions.php";
include "inc/incemail.php";
function readfile_chunked($filename,$retbytes=true) {
	$chunksize = 1*(1024*1024); // how many bytes per chunk
	$buffer = '';
	$cnt =0;
	$handle = fopen($filename, 'rb');
	if ($handle === false) {
		return false;
	}
	while (!feof($handle)) {
		$buffer = fread($handle, $chunksize);
		echo $buffer;
		flush();
		if ($retbytes) {
			$cnt += strlen($buffer);
		}
	}
	$status = fclose($handle);
	if($retbytes && $status) {
		return $cnt;
	}
	return $status;
}
$isdodgy=FALSE;
if(@$htmlemails==TRUE) $emlNl = "<br />"; else $emlNl="\n";
if(@$digidownloadsecret=="") $digidownloadsecret="this is some secwet text";
if(@$digidownloaddays=="") $digidownloaddays=3;
$ref = str_replace("'", "", trim(@$_GET["ref"]));
if($ref=="") $ref="default";
$id = trim(@$_GET["id"]);
if(! is_numeric($id)) $id = 11111;
$pw = trim(str_replace("'","",@$_GET["rd"]));
if($pw=="") $pw = "fg";
$sid = trim(str_replace("'","",@$_GET["sd"]));
if($sid=="") $sid = 11111;
$alreadygotadmin = getadminsettings();
$sSQL = "SELECT ordID,cartID,cartProdId,ordTotal,ordIP,ordEmail FROM cart INNER JOIN orders ON cart.cartOrderID=orders.ordID WHERE ordStatus>=3 AND ordAuthNumber<>'' AND cartCompleted=1 AND cartID='" . escape_string($id) . "' AND cartProdID='" . escape_string($ref) . "' AND cartSessionID='" . escape_string($sid) . "' AND cartDateAdded > '" . date("Y-m-d", (time()-(60*60*24*$digidownloaddays))) . "'";
$result = mysql_query($sSQL) or print(mysql_error());
if($rs = mysql_fetch_array($result)){
	$theamnt = (double)$rs["ordTotal"];
	$ordid = $rs["ordID"];
	$cartid = $rs["cartID"];
	if(@$digitaldownloadlimit != "")
		if($theamnt > $digitaldownloadlimit) $isdodgy=TRUE;
	$fingerprint = vrhmac($digidownloadsecret, $id . $rs["cartProdId"] . $sid);
	if($fingerprint!=$pw) $isdodgy=TRUE;
}else{
	$isdodgy=TRUE;
}
mysql_free_result($result);
function dodgyfunction($thepath){
global $digidownloadwarn,$emailAddr,$ref,$id,$pw,$emlNl,$emailencoding,$htmlemails;
?><HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
<TITLE>Download Error</TITLE>
</HEAD>
<BODY>
<p><font face="Verdana" size="2">If you are trying to download 
  the products you have just purchased, then I am afraid you have received this 
  page in error. Firstly please accept our sincerest apologies. Please email us at 
  <a href="mailto:<?php print $emailAddr?>"><?php print $emailAddr?></a> 
  with the details of your order, and we will send any products you purchased 
  by email.</font></p><?php
	if(@$digidownloadwarn==TRUE){
		$seBody = "Prod Ref: " . $ref . $emlNl . 
			"Order Id: " . $id . $emlNl . 
			"P-word: " . $pw . $emlNl . 
			"User Agent: " . @$_SERVER["HTTP_USER_AGENT"] . $emlNl . 
			"Query String: " . @$_SERVER["QUERY_STRING"] . $emlNl . 
			"IP: " . @$_SERVER["REMOTE_HOST"] . $emlNl;
		if($thepath != ''){
			$seBody .= "Failed to find: " . $thepath . $emlNl;
			$seBody .= "Path to this script : " . $_SERVER["PATH_TRANSLATED"] . $emlNl;
		}
		$headers = "MIME-Version: 1.0\n";
		$headers .= "From: " . $emailAddr . " <" . $emailAddr . ">\n";
		if(@$htmlemails==TRUE)
			$headers .= "Content-type: text/html; charset=".$emailencoding."\n";
		else
			$headers .= "Content-type: text/plain; charset=".$emailencoding."\n";
		mail($emailAddr, "Failed Download", $seBody, $headers);
	}
?>
<p><font face="Verdana" size="2">Please <a href="javascript:history.go(-1)">Click Here</a> to try the download again.</font></p>
</BODY>
</HTML>
<?php
}
if($isdodgy){
	dodgyfunction("");
}else{
	$sSQL = "SELECT pDownload FROM products WHERE pID='" . escape_string($ref) . "'";
	$result = mysql_query($sSQL) or print(mysql_error());
	if($rs = mysql_fetch_array($result)){
		$thedownload=trim($rs["pDownload"]);
	}
	mysql_free_result($result);
	if(strtolower($thedownload)=="a") // auto download
		$strFileName = str_replace("%pid%", $ref, $digidownloadpattern);
	else
		$strFileName = $thedownload;
	$noregexpcol=FALSE;
	$sSQL="SELECT optRegExp FROM cartoptions INNER JOIN options ON cartoptions.coOptID=options.optID WHERE coCartID=" . $cartid . " ORDER BY coCartID";
	$result2 = mysql_query($sSQL) or $noregexpcol=TRUE;
	if($noregexpcol != TRUE){
		while($rs2 = mysql_fetch_assoc($result2)){
			$theexp = trim($rs2["optRegExp"]);
			if(substr($theexp, 0, 1)=="!"){
				$theexp = substr($theexp, 1);
				if(substr($theexp, 0, 1)=="!"){
					$strFileName = "";
				}else{
					$theexp = str_replace('%s', $strFileName, $theexp);
					if(strpos($theexp, " ") !== FALSE){ // Search and replace
						$exparr = split(" ", $theexp, 2);
						$strFileName = str_replace($exparr[0], $exparr[1], $strFileName);
					}else
						$strFileName = $theexp;
				}
			}
		}
	}
	$dlFileName = basename($strFileName);
	if(file_exists($strFileName)){
		// "application/octet-binary"
		header("Content-Type: application/save"); 
		header("Content-Disposition: attachment; filename=" . $dlFileName);
		header("Content-Length: " . filesize($strFileName));
		if($fhan = readfile_chunked($strFileName)){
			// fpassthru($fhan);
		}
	}else{
		dodgyfunction($strFileName);
	}
}
?>