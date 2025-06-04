<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
include "./vsadmin/inc/incemail.php";
$success=FALSE;
$warnexpireddownload=TRUE;
$noshowdigiordertext=TRUE;
$orderText="";
if(@$digidownloadsecret=="") $digidownloadsecret="this is some secret text";
$theordid = trim(str_replace("'", "", @$_GET["ordid"]));
if(trim(@$_POST["ordid"]) != "") $theordid = trim(str_replace("'", "", @$_POST["ordid"]));
$thepass = trim(str_replace("'", "", @$_GET["pass"]));
if(trim(@$_POST["pass"]) != "") $thepass = trim(str_replace("'", "", @$_POST["pass"]));
$isposted=(@$_GET["posted"]=="1" || ($theordid != "" && $thepass != ""));
if($isposted){
	if($theordid != "" && $thepass != "" && is_numeric($theordid)){
		$sSQL = "SELECT ordID,ordAuthNumber,ordSessionID FROM orders WHERE ordID='" . escape_string($theordid) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if($rs = mysql_fetch_assoc($result)){
			$ordID = $rs["ordID"];
			$ordAuthNumber = $rs["ordAuthNumber"];
			$ordSessionID = $rs["ordSessionID"];
			$fingerprint = vrhmac($digidownloadsecret, $ordID . $ordAuthNumber . $ordSessionID);
			$fingerprint = substr($fingerprint, 0, 14);
			if($fingerprint==$thepass){
				$xxRecEml="";
				$success=TRUE;
			}else
				$errmsg=$xxNoLog;
		}else{
			$success=FALSE;
			$errmsg=$xxNoLog;
		}
		if($success){
			// Check there is something to download
			$sSQL="SELECT cartProdID FROM products INNER JOIN cart ON products.pID=cart.cartProdID INNER JOIN orders ON cart.cartOrderID=orders.ordID WHERE ordStatus>=3 AND cartCompleted=1 AND cartOrderID='" . $ordID . "' AND ordAuthNumber='" . escape_string($ordAuthNumber) . "' AND pDownload<>''";
			$result = mysql_query($sSQL) or print(mysql_error());
			if(mysql_num_rows($result)==0){
				$success=FALSE;
				$errmsg="Your order id and password are correct, however there are no downloadable products associated with this order.";
			}
		}
	}else{
		$success=FALSE;
		$errmsg=$xxPlEnt;
	}
}
?>
      &nbsp;<br />
	  <table border="0" cellspacing="0" cellpadding="0" width="100%" bgcolor="#B1B1B1" align="center">
<?php	if($isposted && $success){
			if(FALSE){ ?>
        <tr>
          <td width="100%">
            <table width="100%" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
			  <tr>
				<td colspan="2" bgcolor="#FFFFFF">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr> 
					  <td width="100%" colspan="2" align="center"><br /><strong><?php print $xxLISuc?></strong><br /><br />
						<img src="../images/clearpixel.gif" width="300" height="3" alt="" />
					  </td>
					</tr>
				  </table>
                </td>
			  </tr>
			</table>
		  </td>
        </tr>
<?php		}
		}else{ ?>
        <tr>
		  <form method="post" action="latedownload.php">
		  <td width="100%">
			<input type="hidden" name="posted" value="1" />
            <table class="cobtbl" width="100%" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
			  <tr>
				<td class="cobll" colspan="2" bgcolor="#FFFFFF" height="34">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
					  <td width="14%" align="center"><img src="images/minipadlock.gif" alt="<?php print $xxMLLIS?>" /></td><td width="72%" align="center"><font size="4"><strong><?php print $xxPlEnt?></strong></font></td><td width="14%" align="center" height="30"><img src="images/minipadlock.gif" alt="<?php print $xxMLLIS?>" /></td>
					</tr>
				  </table>
				</td>
			  </tr>
<?php		if($isposted && ! $success){ ?>
			  <tr> 
                <td class="cobll" width="100%" bgcolor="#FFFFFF" height="34" colspan="2" align="center"><font color="#FF0000"><?php print $errmsg?></font></td>
			  </tr>
<?php		} ?>
              <tr> 
                <td class="cobhl" width="40%" bgcolor="#EBEBEB" align="right" height="34"><strong><?php print $xxOrdId?>: </strong></td>
				<td class="cobll" align="left" bgcolor="#FFFFFF" height="34"><input type="text" name="ordid" size="20" value="<?php print $theordid?>" /> </td>
			  </tr>
			  <tr> 
                <td class="cobhl" bgcolor="#EBEBEB" align="right" height="34"><strong><?php print $xxPwd?>: </strong></td>
				<td class="cobll" align="left" bgcolor="#FFFFFF" height="34"><input type="password" name="pass" size="20" value="<?php print $thepass?>" /> </td>
			  </tr>
			  <tr> 
                <td class="cobll" width="100%" colspan="2" align="center" bgcolor="#FFFFFF" height="34"><input type="submit" value="<?php print $xxSubmt?>" /><br />
				<img src="../images/clearpixel.gif" width="300" height="1" alt="" /></td>
			  </tr>
            </table>
		  </td>
		  </form>
        </tr>
<?php	} ?>
      </table><br />&nbsp;
<?php
require "./vsadmin/inc/digidownload.php";
?>