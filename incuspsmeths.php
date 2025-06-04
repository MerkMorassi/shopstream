<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
if(@$storesessionvalue=="") $storesessionvalue="virtualstore".time();
if($_SESSION["loggedon"] != $storesessionvalue || @$disallowlogin==TRUE) exit;
$success=TRUE;
$alreadygotadmin = getadminsettings();
$method=trim(@$_REQUEST["method"]);
if($method!='') $shipType=(int)$method;
$shipmet = "USPS";
if($shipType==4) $shipmet = "UPS";
if($shipType==6) $shipmet = $yyCanPos;
if($shipType==7) $shipmet = "FedEx";
if($shipType==8) $shipmet = 'FedEx SmartPost';
if($shipType==9) $shipmet = "DHL";
function checkisdocument($st,$serv){
	$cid='';
	if($st==9){
		if($serv=='2' || $serv=='5' || $serv=='6' || $serv=='7' || $serv=='9' || $serv=='B' || $serv=='C' || $serv=='D' || $serv=='G' || $serv=='I' || $serv=='K' || $serv=='L' || $serv=='N' || $serv=='R' || $serv=='S' || $serv=='T' || $serv=='U' || $serv=='W' || $serv=='X')
			$cid=' <strong>(document)</strong>';
	}
	return($cid);
}
if(@$_POST["posted"]=="1"){
	if(@$_POST['doadmin']!=''){
		if($shipType==3){
			$sSQL="UPDATE admin SET adminUSPSUser='".escape_string(@$_POST['adminUSPSUser'])."' WHERE adminID=1";
			mysql_query($sSQL) or print(mysql_error());
		}elseif($shipType==4){
			$sSQL="UPDATE admin SET adminUPSNegotiated=".@$_POST['UPSNegotiated']." WHERE adminID=1";
			mysql_query($sSQL) or print(mysql_error());
		}elseif($shipType==6){
			$sSQL="UPDATE admin SET adminCanPostUser='".escape_string(@$_POST['adminCanPostUser'])."' WHERE adminID=1";
			mysql_query($sSQL) or print(mysql_error());
		}elseif($shipType==9){
			$sSQL="UPDATE admin SET DHLSiteID='" . escape_string(@$_POST['DHLSiteID']) . "',DHLSitePW='" . escape_string(@$_POST['DHLSitePW']) . "',DHLAccountNo='" . escape_string(@$_POST['DHLAccountNo']) . "' WHERE adminID=1";
			mysql_query($sSQL) or print(mysql_error());
		}
	}else{
		if($shipType==3){
			for($index=1;$index<=50;$index++){
				if(trim(@$_POST['methodshow' . $index])!=''){
					$sSQL = "UPDATE uspsmethods SET uspsShowAs='" . escape_string(unstripslashes(@$_POST['methodshow' . $index])) . "',";
					if(@$_POST['methodfsa' . $index]=='ON')
						$sSQL .= 'uspsFSA=1,';
					else
						$sSQL .= 'uspsFSA=0,';
					if(@$_POST['methoduse' . $index]=='ON')
						$sSQL .= 'uspsUseMethod=1 WHERE uspsID=' . $index;
					else
						$sSQL .= 'uspsUseMethod=0 WHERE uspsID=' . $index;
					mysql_query($sSQL) or print(mysql_error());
				}
			}
		}elseif($shipType==4 || $shipType==6 || $shipType==7 || $shipType==8 || $shipType==9){
			$indexadd=0;
			if($shipType==6) $indexadd=100; elseif($shipType==7) $indexadd=200; elseif($shipType==8) $indexadd=300; elseif($shipType==9) $indexadd=400;
			for($index=100+$indexadd;$index<=155+$indexadd;$index++){
				if(trim(@$_POST['methodshow' . $index])!=''){
					$sSQL = 'UPDATE uspsmethods SET ';
					if(@$_POST['methodfsa' . $index]=='ON')
						$sSQL .= 'uspsFSA=1,';
					else
						$sSQL .= "uspsFSA=0,";
					if(@$_POST["methoduse" . $index]=="ON")
						$sSQL .= "uspsUseMethod=1 WHERE uspsID=" . $index;
					else
						$sSQL .= "uspsUseMethod=0 WHERE uspsID=" . $index;
					mysql_query($sSQL) or print(mysql_error());
				}
			}
		}
	}
	print '<meta http-equiv="refresh" content="2; url=adminuspsmeths.php">';
}
if(@$_GET['royalmail']=='setup'){ ?>
<p>&nbsp;</p>
<p align="center">Proceeding will replace all your weight based shipping tables with Royal Mail 2012 rates</p>
<p align="center">Product weights are assumed to be in metric (kg)</p>
<p align="center">Please note, clicking below will wipe all your current postal zone inforamtion and cannot be undone.</p>
<p>&nbsp;</p>
<form method="post" action="adminuspsmeths.php">
<input type="hidden" name="royalmail" value="dosetup" />
<p align="center">
<table border="0" width="100%">
<tr><td align="right" width="25%"><input type="checkbox" name="addrecorded" value="ON" /></td><td align="left">Add Recorded Signed For option to First, Second and Standard Parcel rates? (&pound;0.95 extra)</td></tr>
<tr><td align="right"><input type="checkbox" name="addinternationalsigned" value="ON" /></td><td align="left">Add International Signed For option to International rates? (&pound;5.15 extra)</td></tr>
<tr><td align="right"><input type="checkbox" name="addspecial" value="ON" /></td><td align="left">Add Special Delivery 9am and 1pm Services?</td></tr>
</table>
</p>
<p>&nbsp;</p>
<p align="center"><input type="submit" value="Apply Royal Mail Rates" /></p>
</form>
<p>&nbsp;</p>
<?php
}elseif(@$_POST['royalmail']=='dosetup'){ ?>
<p>&nbsp;</p>
<p align="center">The process has completed successfully</p>
<p align="center">You still need to select "Weight Based Shipping" as your shipping method in the admin main settings page.</p>
<p>&nbsp;</p>
<?php
	$addrecorded=FALSE;
	$addinternationalsigned=FALSE;
	if(@$_POST['addrecorded']=='ON') $addrecorded=TRUE;
	if(@$_POST['addinternationalsigned']=='ON') $addinternationalsigned=TRUE;
	function doaddrate($zczone,$zcweight,$zcrate,$zcrate2,$zcrate3,$zcrate4){
		global $addrecorded,$addinternationalsigned;
		if($zczone==1 && $addrecorded && $zcrate>0) $zcrate+=0.95;
		if($zczone==1 && $addrecorded && $zcrate2>0) $zcrate2+=0.95;
		if($zczone>1 && $addinternationalsigned && $zcrate>0) $zcrate+=5.15;
		mysql_query("INSERT INTO zonecharges (zcZone,zcWeight,zcRate,zcRate2,zcRate3,zcRate4) VALUES (" . $zczone . "," . $zcweight . "," . $zcrate . "," . $zcrate2 . "," . $zcrate3 . "," . $zcrate4 . ")") or print(mysql_error());
	}
	function addpostalzone($zoneid,$pzname,$pzmultishipping,$pzmethodname1,$pzmethodname2,$pzmethodname3,$pzmethodname4){
		$sSQL="UPDATE postalzones SET pzName='" . $pzname . "',pzMultiShipping=" . $pzmultishipping . ",pzMethodName1='" . $pzmethodname1 . "',pzMethodName2='" . $pzmethodname2 . "',pzMethodName3='" . $pzmethodname3 . "',pzMethodName4='" . $pzmethodname4 . "' WHERE pzID=" . $zoneid;
		mysql_query($sSQL) or print(mysql_error());
		return($zoneid);
	}

	mysql_query("DELETE FROM zonecharges") or print(mysql_error());
	mysql_query("UPDATE admin SET adminUSZones=0") or print(mysql_error());
	mysql_query("UPDATE countries SET countryZone=99999") or print(mysql_error());
	
	$zoneid = addpostalzone(1,"Great Britain",@$_POST['addspecial']=='ON'?3:1,"First Class","Second Class","Special Delivery Next Day (1:00pm)","Special Delivery Next Day (9:00am)");
	doaddrate(1,-2,3.5,0,0,0);
	doaddrate(1,0.1,0.9,0.69,5.9,16.7);
	doaddrate(1,0.25,1.2,1.1,6.35,18.8);
	doaddrate(1,0.5,1.6,1.4,6.35,18.8);
	doaddrate(1,0.75,2.7,2.2,7.55,20.45);
	doaddrate(1,1,4.3,3.5,7.55,20.45);
	doaddrate(1,1.25,5.6,5.3,9.75,24.75);
	doaddrate(1,1.5,6.5,5.3,9.75,24.75);
	doaddrate(1,1.75,7.4,5.3,9.75,24.75);
	doaddrate(1,2,8.3,5.3,9.75,24.75);
	doaddrate(1,4,10.3,8.8,24.5,-99999);
	doaddrate(1,6,13.8,12.3,24.5,-99999);
	doaddrate(1,8,17.3,15.8,24.5,-99999);
	doaddrate(1,10,20.8,18.8,24.5,-99999);
	doaddrate(1,12,24.3,21.9,-99999,-99999);
	doaddrate(1,14,27.8,21.9,-99999,-99999);
	doaddrate(1,16,31.3,21.9,-99999,-99999);
	doaddrate(1,18,34.8,21.9,-99999,-99999);
	doaddrate(1,20,38.3,21.9,-99999,-99999);
	doaddrate(1,22,41.8,-99999,-99999,-99999);
	 
	mysql_query("UPDATE countries SET countryZone=" . $zoneid . " WHERE countryID IN (107,142,201,214,216)") or print(mysql_error());
	
	$zoneid = addpostalzone(2,"Europe",0,"Standard Shipping","","","");
	// doaddrate(2,-0.1,0.6,0.8,0,0);
	doaddrate(2,0.1, 2.70,0,0,0);
	doaddrate(2,0.15,2.93,0,0,0);
	doaddrate(2,0.2, 3.16,0,0,0);
	doaddrate(2,0.3, 3.62,0,0,0);
	doaddrate(2,0.4, 4.22,0,0,0);
	doaddrate(2,0.5, 4.82,0,0,0);
	doaddrate(2,0.6, 5.42,0,0,0);
	doaddrate(2,0.7, 6.02,0,0,0);
	doaddrate(2,0.8, 6.62,0,0,0);
	doaddrate(2,0.9, 7.22,0,0,0);
	doaddrate(2,1.0, 7.82,0,0,0);
	doaddrate(2,1.1, 8.42,0,0,0);
	doaddrate(2,1.2, 9.02,0,0,0);
	doaddrate(2,1.3, 9.62,0,0,0);
	doaddrate(2,1.4,10.22,0,0,0);
	doaddrate(2,1.5,10.82,0,0,0);
	doaddrate(2,1.6,11.42,0,0,0);
	doaddrate(2,1.7,12.02,0,0,0);
	doaddrate(2,1.8,12.62,0,0,0);
	doaddrate(2,1.9,13.22,0,0,0);
	doaddrate(2,2.0,13.82,0,0,0);
	for($indexar=1; $indexar <= 30; $indexar++)
		doaddrate(2,2+($indexar/10.0),13.82+($indexar*0.6),0,0,0);
	doaddrate(2,5.01,-99999,0,0,0);
	mysql_query("UPDATE countries SET countryZone=" . $zoneid . " WHERE countryID IN (4,6,12,15,16,21,22,28,32,46,48,49,50,59,62,64,65,70,71,73,74,75,86,87,91,93,97,103,108,109,110,112,118,123,124,133,143,152,153,156,157,163,175,170,171,175,182,183,186,194,195,199,203,205,217,218,219,221,223)") or print(mysql_error());
	
	$zoneid = addpostalzone(3,"World Zone 1",0,"Standard Shipping","","","");
	// doaddrate(3,-0.1,1.22,0,0,0);
	doaddrate(3,0.15,3.86,0,0,0);
	doaddrate(3,0.2, 4.42,0,0,0);
	doaddrate(3,0.25,4.98,0,0,0);
	doaddrate(3,0.3, 5.54,0,0,0);
	doaddrate(3,0.4, 6.76,0,0,0);
	doaddrate(3,0.5, 7.98,0,0,0);
	doaddrate(3,0.6, 9.20,0,0,0);
	doaddrate(3,0.7,10.42,0,0,0);
	doaddrate(3,0.8,11.64,0,0,0);
	doaddrate(3,0.9,12.86,0,0,0);
	doaddrate(3,1.0,14.08,0,0,0);
	doaddrate(3,1.1,15.30,0,0,0);
	doaddrate(3,1.2,16.52,0,0,0);
	doaddrate(3,1.3,17.74,0,0,0);
	doaddrate(3,1.4,18.96,0,0,0);
	doaddrate(3,1.5,20.18,0,0,0);
	doaddrate(3,1.6,21.40,0,0,0);
	doaddrate(3,1.7,22.62,0,0,0);
	doaddrate(3,1.8,23.84,0,0,0);
	doaddrate(3,1.9,25.06,0,0,0);
	doaddrate(3,2.0,26.28,0,0,0);
	for($indexar=1; $indexar <= 30; $indexar++)
		doaddrate(3,2+($indexar/10.0),26.28+($indexar*1.22),0,0,0);
	doaddrate(3,5.01,-99999,0,0,0);
	mysql_query("UPDATE countries SET countryZone=" . $zoneid . " WHERE countryZone=99999") or print(mysql_error());
	
	$zoneid = addpostalzone(4,"World Zone 2",0,"Standard Shipping","","","");
	// doaddrate(4,-0.1,1.28,0,0,0);
	doaddrate(4,0.15,3.90,0,0,0);
	doaddrate(4,0.2, 4.50,0,0,0);
	doaddrate(4,0.25,5.10,0,0,0);
	doaddrate(4,0.3, 5.70,0,0,0);
	doaddrate(4,0.4, 6.98,0,0,0);
	doaddrate(4,0.5, 8.26,0,0,0);
	doaddrate(4,0.6, 9.54,0,0,0);
	doaddrate(4,0.7,10.82,0,0,0);
	doaddrate(4,0.8,12.10,0,0,0);
	doaddrate(4,0.9,13.38,0,0,0);
	doaddrate(4,1.0,14.66,0,0,0);
	doaddrate(4,1.1,15.94,0,0,0);
	doaddrate(4,1.2,17.22,0,0,0);
	doaddrate(4,1.3,18.50,0,0,0);
	doaddrate(4,1.4,19.78,0,0,0);
	doaddrate(4,1.5,21.06,0,0,0);
	doaddrate(4,1.6,22.34,0,0,0);
	doaddrate(4,1.7,23.62,0,0,0);
	doaddrate(4,1.8,24.90,0,0,0);
	doaddrate(4,1.9,26.18,0,0,0);
	doaddrate(4,2.0,27.46,0,0,0);
	for($indexar=1; $indexar <= 30; $indexar++)
		doaddrate(4,2+($indexar/10.0),27.46+($indexar*1.28),0,0,0);
	doaddrate(4,5.01,-99999,0,0,0);
	mysql_query("UPDATE countries SET countryZone=" . $zoneid . " WHERE countryID IN (14,63,67,99,111,131,135,136,140,141,147,151,162,169,172,190,191,197)") or print(mysql_error());
}elseif(@$_GET['admin']!=''){
	$sSQL = 'SELECT adminUSPSUser,adminUPSUser,adminUPSPw,adminUPSAccess,adminUPSAccount,adminUPSNegotiated,adminCanPostUser,DHLSiteID,DHLSitePW,DHLAccountNo FROM admin WHERE adminID=1';
	$result = mysql_query($sSQL) or print(mysql_error());
	$rs = mysql_fetch_assoc($result);
?>
		  <form method="post" action="adminuspsmeths.php">
<?php
	writehiddenvar('doadmin', '1');
	writehiddenvar('method', @$_GET['admin']);
	writehiddenvar('posted', '1'); ?>
			<table width="100%" border="0" cellspacing="2" cellpadding="3">
			  <tr>
                <td colspan="2">&nbsp;</td>
			  </tr>
<?php
	if(@$_GET['admin']=='3'){ ?>
			  <tr>
                <td colspan="2" align="center"><strong>USPS Admin</strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%" /><?php print $yyIfUSPS?><br /></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong><?php print $yyUname?>: </strong></td>
				<td width="50%" align="left"><input type="text" size="15" name="adminUSPSUser" value="<?php print $rs['adminUSPSUser']?>" /></td>
			  </tr>
<?php
	}elseif(@$_GET['admin']=='4'){ ?>
			  <tr>
                <td colspan="2" align="center"><strong>UPS Admin</strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%" /><p>To obtain your UPS Rate Code you need to use the registration form <a href="adminupslicense.php"><strong>here</strong></a>.</p>
				<p>To use UPS Negotiated Rates, you need to register first and specify your UPS Shipper Number in the registration form. Then forward your UPS Rate Code and Shipper Number to your UPS Account Manager who will enable UPS Negotiated Rates once approved.</p></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong>UPS Rate Code: </strong></td>
				<td width="50%" align="left"><?php print upsdecode($rs['adminUPSUser'], '')?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong>UPS Shipper Number: </strong></td>
				<td width="50%" align="left"><?php print $rs['adminUPSAccount']?></td>
			  </tr>
			  <tr>
				<td width="50%" align="right"><strong>Use Negotiated Rates: </strong></td>
				<td width="50%" align="left"><select size="1" name="UPSNegotiated">
					<option value="0">Use Published Rates</option>
<?php	if(trim($rs['adminUPSUser'])!='' && trim($rs['adminUPSAccount'])!='') print '<option value="1"' . ((int)$rs['adminUPSNegotiated']!=0 ? ' selected="selected"' : '') . '>Use Negotiated Rates</option>' ?>
					</select>
				</td>
			  </tr>
<?php
	}elseif(@$_GET['admin']=='6'){ ?>
			  <tr>
                <td colspan="2" align="center"><strong><?php print $yyCanPos?> Admin</strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><hr width="70%" /><?php print $yyEnMerI?></td>
			  </tr>
			  <tr>
				<td colspan="2" align="center"><strong><?php print $yyRetID?>: </strong><input type="text" size="36" name="adminCanPostUser" value="<?php print $rs['adminCanPostUser']?>" /></td>
			  </tr>
<?php
	}elseif(@$_GET['admin']=='9'){ ?>
			  <tr>
                <td colspan="2" align="center"><strong>DHL Admin</strong><br />&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2">&nbsp;</td>
			  </tr>
			  <tr>
				<td align="right" width="45%"><strong>Site ID: </strong></td><td><input type="text" size="36" name="DHLSiteID" value="<?php print $rs['DHLSiteID']?>" /></td>
			  </tr>
			  <tr>
				<td align="right"><strong>Site Password: </strong></td><td><input type="password" size="36" name="DHLSitePW" value="<?php print $rs['DHLSitePW']?>" /></td>
			  </tr>
			  <tr>
				<td align="right"><strong>Account Number: </strong></td><td><input type="text" size="36" name="DHLAccountNo" value="<?php print $rs['DHLAccountNo']?>" /></td>
			  </tr>
<?php
	} ?>
			  <tr>
				<td width="100%" align="center" colspan="2">&nbsp;</td>
			  </tr>
			  <tr>
				<td width="100%" align="center" colspan="2"><input type="submit" value="<?php print $yySubmit?>" /> <input type="reset" value="<?php print $yyReset?>" /></td>
			  </tr>
<?php
	if(@$_GET['admin']=='4'){ ?>
			  <tr>
				<td width="100%" align="center" colspan="2"><br /><span style="font-size:10px">Please note: Subsequent registrations for UPS OnLine® Tools will change the UPS Rate Code
within this application. In the event Negotiated Rates functionality was enabled under a previous UPS Rate Code, the
Negotiated Rates functionality will be disabled.</span></td>
			  </tr>
<?php
	} ?>
			  <tr>
				<td width="100%" align="center" colspan="2"><br />&nbsp;<br />&nbsp;<br /><a href="adminuspsmeths.php"><strong><?php print $yyAdmHom?></strong></a><br />&nbsp;</td>
			  </tr>
			</table>
		  </form>
<?php
	mysql_free_result($result);
}elseif($method==''){ ?>
			<table width="100%" border="0" cellspacing="2" cellpadding="3">
			  <tr>
                <td align="center">
					&nbsp;<br /><strong><?php print $yyUsUpd . ' ' . $yyShpMet?>.</strong><br />&nbsp;<br />&nbsp;
			
			<table width="90%" border="0" cellspacing="1" cellpadding="3" class="cobtbl">
			  <tr>
				<td align="center" class="cobhl" height="35"><strong>Shipping Carrier</strong></td>
				<td align="center" class="cobhl"><strong>Registration</strong></td>
				<td align="center" class="cobhl"><strong>Administration</strong></td>
				<td align="center" class="cobhl"><strong>Shipping Method</strong></td>
			  </tr>
			  <tr>
				<td align="center" class="cobhl" height="30"><strong>DHL</strong></td>
				<td class="cobll">&nbsp; </td>
				<td align="center" class="cobll"><input type="button" value="DHL Admin" onclick="document.location='adminuspsmeths.php?admin=9'" /></td>
				<td align="center" class="cobll"><input type="button" value="<?php print $yyEdit.' '.$yyShpMet?>" onclick="document.location='adminuspsmeths.php?method=9'" /></td>
			  </tr>
			  <tr>
				<td align="center" class="cobhl" height="30"><strong>Canada Post</strong></td>
				<td class="cobll">&nbsp; </td>
				<td align="center" class="cobll"><input type="button" value="Canada Post Admin" onclick="document.location='adminuspsmeths.php?admin=6'" /></td>
				<td align="center" class="cobll"><input type="button" value="<?php print $yyEdit.' '.$yyShpMet?>" onclick="document.location='adminuspsmeths.php?method=6'" /></td>
			  </tr>
			  <tr>
				<td align="center" class="cobhl" height="30"><strong>FedEx</strong></td>
				<td align="center" class="cobll"><input type="button" value="<?php print str_replace("UPS","FedEx",$yyRegUPS)?>" onclick="document.location='adminfedexlicense.php'" /></td>
				<td align="center" class="cobll">&nbsp;</td>
				<td align="center" class="cobll"><input type="button" value="<?php print $yyEdit.' '.$yyShpMet?>" onclick="document.location='adminuspsmeths.php?method=7'" /></td>
			  </tr>
			  <tr>
				<td align="center" class="cobhl" height="30"><strong>FedEx SmartPost</strong></td>
				<td align="center" class="cobll"><input type="button" value="<?php print str_replace("UPS","FedEx",$yyRegUPS)?>" onclick="document.location='adminfedexlicense.php'" /></td>
				<td align="center" class="cobll">&nbsp;</td>
				<td align="center" class="cobll"><input type="button" value="<?php print $yyEdit.' '.$yyShpMet?>" onclick="document.location='adminuspsmeths.php?method=8'" /></td>
			  </tr>
			  <tr>
				<td align="center" class="cobhl" height="30"><strong>USPS</strong></td>
				<td align="center" class="cobll"><input type="button" value="Register with USPS" onclick="window.open('https://secure.shippingapis.com/registration/','USPSSignup','')" /></strong></td>
				<td align="center" class="cobll"><input type="button" value="USPS Admin" onclick="document.location='adminuspsmeths.php?admin=3'" /></td>
				<td align="center" class="cobll"><input type="button" value="<?php print $yyEdit.' '.$yyShpMet?>" onclick="document.location='adminuspsmeths.php?method=3'" /></td>
			  </tr>
			  <tr>
				<td align="center" class="cobhl" height="30"><strong>UPS</strong></td>
				<td align="center" class="cobll"><input type="button" value="<?php print $yyRegUPS?>" onclick="document.location='adminupslicense.php'" /></td>
				<td align="center" class="cobll"><input type="button" value="UPS Admin" onclick="document.location='adminuspsmeths.php?admin=4'" /></td>
				<td align="center" class="cobll"><input type="button" value="<?php print $yyEdit.' '.$yyShpMet?>" onclick="document.location='adminuspsmeths.php?method=4'" /></td>
			  </tr>
			  <tr>
				<td align="center" class="cobhl" height="30"><strong>Weight / Price Based</strong></td>
				<td class="cobll">&nbsp; </td>
				<td class="cobll">&nbsp; </td>
				<td align="center" class="cobll"><input type="button" value="<?php print $yyEdit.' Postal Zones'?>" onclick="document.location='adminzones.php'" /></td>
			  </tr>
			</table>

			<br /><p align="center"><a href="adminuspsmeths.php?royalmail=setup">Setup weight based shipping to use Royal Mail rates</p><br />
			
			<br />&nbsp;<br />&nbsp;<br /><a href="admin.php"><strong><?php print $yyAdmHom?></strong></a><br />&nbsp;
			
				</td>
			  </tr>
			</table>
			<br />&nbsp;
<?php
}elseif(@$_POST["posted"]=="1" && $success){ ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
			  <tr> 
                <td width="100%" colspan="2" align="center"><br /><strong><?php print $yyUpdSuc?></strong><br /><br /><?php print $yyNowFrd?><br /><br />
                        <?php print $yyNoAuto?> <a href="admin.php"><strong><?php print $yyClkHer?></strong></a>.<br /><br />&nbsp;
                </td>
			  </tr>
			</table>
<?php
}else{ ?>
		  <form method="post" action="adminuspsmeths.php">
			<input type="hidden" name="posted" value="1" />
			<input type="hidden" name="method" value="<?php print $method?>" />
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr> 
                <td width="100%" colspan="5" align="center"><br /><strong><?php print $yyUsUpd . " " . $shipmet . " " . $yyShpMet?>.</strong><br />&nbsp;</td>
			  </tr>
<?php	if(! $success){ ?>
			  <tr> 
                <td width="100%" colspan="5" align="center"><br /><span style="color:#FF0000"><?php print $errmsg?></span>
                </td>
			  </tr>
<?php	}
		$sSQL = 'SELECT uspsID,uspsMethod,uspsShowAs,uspsUseMethod,uspsLocal,uspsFSA FROM uspsmethods ';
		if($shipType==3)
			$sSQL .= 'WHERE uspsID<100 ';
		elseif($shipType==4)
			$sSQL .= 'WHERE uspsID>100 AND uspsID<200 ';
		elseif($shipType==6)
			$sSQL .= 'WHERE uspsID>200 AND uspsID<300 ';
		elseif($shipType==7)
			$sSQL .= 'WHERE uspsID>300 AND uspsID<400 ';
		elseif($shipType==8)
			$sSQL .= 'WHERE uspsID>400 AND uspsID<500 ';
		elseif($shipType==9)
			$sSQL .= 'WHERE uspsID>500 AND uspsID<600 ';
		$sSQL .= 'ORDER BY uspsLocal DESC, uspsShowAs, uspsID';
		$result = mysql_query($sSQL) or print(mysql_error());
		if($shipType==3){
?>
			  <tr>
				<td colspan="5"><ul><li><span style="font-size:10px"><?php print $yyUSS1?></span></li>
				<li><span style="font-size:10px"><?php print $yyUSS2?> 
				<a href="http://www.usps.com">http://www.usps.com</a>.</span></li></ul></td>
			  </tr>
<?php		while($allmethods=mysql_fetch_assoc($result)){ ?>
			  <tr>
			    <td align="right"><?php print $yyUSPSMe?>:</td>
				<td align="left"><span style="font-size:10px;font-weight:bold"><?php
					if($allmethods['uspsID']=='1')
						print 'Express Mail';
					elseif($allmethods['uspsID']=='2')
						print 'Priority Mail';
					elseif($allmethods['uspsID']=='3')
						print 'Parcel Post';
					elseif($allmethods['uspsID']=='14')
						print 'Media Mail';
					elseif($allmethods['uspsID']=='15')
						print 'Bound Printed Matter';
					elseif($allmethods['uspsID']=='16')
						print 'First Class Mail';
					elseif($allmethods['uspsID']=='30')
						print 'Global Express Guaranteed Document';
					elseif($allmethods['uspsID']=='31')
						print 'Global Express Guaranteed Non-Document Rectangular';
					elseif($allmethods['uspsID']=='32')
						print 'Global Express Guaranteed Non-Document Non-Rectangular';
					elseif($allmethods['uspsID']=='33')
						print 'Express Mail International (EMS)';
					elseif($allmethods['uspsID']=='34')
						print 'Express Mail International (EMS) Flat Rate Envelope';
					elseif($allmethods['uspsID']=='35')
						print 'Priority Mail International';
					elseif($allmethods['uspsID']=='36')
						print 'Priority Mail International Flat Rate Envelope';
					elseif($allmethods['uspsID']=='37')
						print 'Priority Mail International Regular Flat-Rate Boxes';
					elseif($allmethods['uspsID']=='38')
						print 'First Class Mail International Letters';
					elseif($allmethods['uspsID']=='39')
						print 'First Class Mail International Large Envelope';
					elseif($allmethods['uspsID']=='40')
						print 'First Class Mail International Package';
					elseif($allmethods['uspsID']=='41')
						print 'Priority Mail International Large Flat-Rate Box';
					elseif($allmethods['uspsID']=='42')
						print 'Priority Mail International Small Flat Rate Box';
					elseif($allmethods['uspsID']=='43')
						print 'Express Mail International Legal Flat Rate Envelope';
					elseif($allmethods['uspsID']=='44')
						print 'Priority Mail International Small Flat Rate Envelope';
					elseif($allmethods['uspsID']=='45')
						print 'Priority Mail International DVD Flat Rate Box';
					elseif($allmethods['uspsID']=='46')
						print 'Express Mail International Flat Rate Box';
					else
						print $allmethods['uspsID'];
					?></span></td>
				<td align="center"><?php print $yyUseMet?></td>
				<td align="center"><acronym title="<?php print $yyFSApp?>"><?php print $yyFSA?></acronym></td>
				<td align="center"><?php print $yyType?></td>
			  </tr>
			  <tr>
			    <td align="right"><?php print $yyShwAs?>:</td>
				<td align="left"><input type="text" name="methodshow<?php print $allmethods["uspsID"]?>" value="<?php print $allmethods["uspsShowAs"]?>" size="36" /></td>
				<td align="center"><input type="checkbox" name="methoduse<?php print $allmethods["uspsID"]?>" value="ON" <?php if((int)$allmethods["uspsUseMethod"]==1) print 'checked="checked"'?> /></td>
				<td align="center"><input type="checkbox" name="methodfsa<?php print $allmethods["uspsID"]?>" value="ON" <?php if((int)$allmethods["uspsFSA"]==1) print 'checked="checked"'?> /></td>
				<td align="center"><?php if($allmethods["uspsLocal"]==1) print '<span style="color:#FF0000">Domestic</span>'; else print '<span style="color:#0000FF">Internat.</span>';?></td>
			  </tr>
			  <tr>
				<td colspan="5" align="center"><hr width="80%" /></td>
			  </tr>
<?php		}
		}else{
			if($shipType==4){ ?>
			  <tr>
				<td colspan="5"><ul><li><span style="font-size:10px"><?php print $yyUSS3?></span></li>
				<li><span style="font-size:10px"><?php print str_replace("USPS","UPS",$yyUSS2)?> 
				<a href="http://www.ups.com">http://www.ups.com</a>.</span></li></ul></td>
			  </tr>
<?php		}else{ ?>
			  <tr>
				<td colspan="5"><ul><li><span style="font-size:10px">You can use this page to set which <?php print $shipmet?> shipping methods qualify for free shipping discounts by checking the FSA (Free Shipping Available) checkbox.</span></li>
				<li><span style="font-size:10px"><?php
			print str_replace("USPS",$shipmet,$yyUSS2);
			if($shipType==6){ ?>
				<a href="http://www.canadapost.ca" target="_blank">http://www.canadapost.ca</a>.
<?php		}elseif($shipType==9){ ?>
				<a href="http://www.dhl.com" target="_blank">http://www.dhl.com</a>.
<?php		}else{ ?>
				<a href="http://www.fedex.com" target="_blank">http://www.fedex.com</a>.
<?php		} ?>
				</span></li>
				</ul></td>
			  </tr>
<?php		}
			while($allmethods=mysql_fetch_assoc($result)){ ?>
			  <tr>
			    <td align="right"><input type="hidden" name="methodshow<?php print $allmethods["uspsID"]?>" value="1" /><strong><?php print $yyShipMe?>:</strong></td>
				<td align="left"> &nbsp; <?php print $allmethods["uspsShowAs"] . checkisdocument($shipType,$allmethods['uspsMethod'])?></td>
				<td align="center"><strong><?php print ($shipType==4 || $shipType==7 || $shipType==9?$yyUseMet:"&nbsp;")?></strong></td>
				<td align="center"><acronym title="<?php print $yyFSApp?>"><?php print $yyFSA?></acronym></td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td colspan="2">&nbsp;</td>
				<td align="center"><input type="<?php print ($shipType==4 || $shipType==7 || $shipType==9?"checkbox":"hidden")?>" name="methoduse<?php print $allmethods["uspsID"]?>" value="ON" <?php if((int)$allmethods["uspsUseMethod"]==1) print 'checked="checked"'?> /></td>
				<td align="center"><input type="checkbox" name="methodfsa<?php print $allmethods["uspsID"]?>" value="ON" <?php if((int)$allmethods["uspsFSA"]==1) print 'checked="checked"'?> /></td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td colspan="5" align="center"><hr width="80%" /></td>
			  </tr>
<?php		}
		}
		mysql_free_result($result); ?>
			  <tr> 
                <td width="100%" colspan="5" align="center"><br /><input type="submit" value="<?php print $yySubmit?>" /><br />&nbsp;</td>
			  </tr>
            </table>
		  </form>
<?php
} ?>