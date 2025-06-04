<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property of Internet
//Business Solutions SL. Any use, reproduction, disclosure or copying of any kind 
//without the express and written permission of Internet Business Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
$dltext = "";
if(@$digidownloaddays=="") $digidownloaddays=3;
	if($success && @$digidownloads==TRUE){
		if(@$digidownloadsecret=="") $digidownloadsecret="this is some secwet text";
		$sSQL="SELECT cartID,cartProdID,cartProdName,cartProdPrice,cartSessionID,pDownload FROM products INNER JOIN cart ON products.pID=cart.cartProdID INNER JOIN orders ON cart.cartOrderID=orders.ordID WHERE ordStatus>=3 AND cartCompleted=1 AND cartOrderID='" . $ordID . "' AND ordAuthNumber='" . escape_string(trim($ordAuthNumber)) . "' AND pDownload<>'' AND ordDate > '" . date("Y-m-d", (time()-(60*60*24*$digidownloaddays))) . "'";
		$result = mysql_query($sSQL) or print(mysql_error());
		if(mysql_num_rows($result) > 0){
			while($rs = mysql_fetch_assoc($result)){
				$theid = $rs['cartProdID'];
				$hasdownload = TRUE;
				$noregexpcol=FALSE;
				$fingerprint = vrhmac($digidownloadsecret, $rs['cartID'] . $theid . $rs['cartSessionID']);
				$thedownload=trim($rs['pDownload']);
				if(strtolower($thedownload)=='a'){ // auto download
					$thedownload = str_replace("%pid%", $theid, $digidownloadpattern);
				}
				$sSQL="SELECT optRegExp FROM cartoptions INNER JOIN options ON cartoptions.coOptID=options.optID WHERE coCartID=" . $rs["cartID"] . " ORDER BY coCartID";
				$result2 = mysql_query($sSQL) or $noregexpcol=TRUE;
				if($noregexpcol != TRUE){
					while($rs2 = mysql_fetch_assoc($result2)){
						$theexp = trim($rs2["optRegExp"]);
						if(substr($theexp, 0, 1)=="!"){
							$theexp = substr($theexp, 1);
							if(substr($theexp, 0, 1)=="!"){
								$hasdownload = FALSE;
							}else{
								$theexp = str_replace('%s', $thedownload, $theexp);
								if(strpos($theexp, " ") !== FALSE){ // Search and replace
									$exparr = split(" ", $theexp, 2);
									$thedownload = str_replace($exparr[0], $exparr[1], $thedownload);
								}else
									$thedownload = $theexp;
							}
						}
					}
				}
				mysql_free_result($result2);
				if($hasdownload){
					$dltext .= '<tr><td class="cobll" align="left" bgcolor="#FFFFFF">' . $theid . '</td>';
					$dltext .= '<td class="cobll" align="left" bgcolor="#FFFFFF">' . $rs["cartProdName"] . '</td>';
					$dltext .= '<td class="cobll" align="center" bgcolor="#FFFFFF">';
					if(@$digidownloadmethod=="filesystem"){
						$dltext .= '<a class="ectlink" href="' . $storeurl . 'vsadmin/dodownload.php?ref=' . $theid . '&id=' . $rs['cartID'] . '&sd=' . $rs["cartSessionID"] . '&rd=' . $fingerprint . '" onmouseover="(window.status=\'' . str_replace("'", "\'", $xxDlPro) . '\'); return true" onmouseout="(window.status=\'\'); return true"><font size="4" color="#FF0000"><strong>' . $xxDlPro . '</strong></font></a>';
					}else{
						$dltext .= '<a class="ectlink" href="' . str_replace('"', "&quot;", $thedownload) . '" onmouseover="(window.status=\'' . str_replace("'", "\'", $xxDlPro) . '\'); return true" onmouseout="(window.status=\'\'); return true"><font size="4" color="#FF0000"><strong>' . $xxDlPro . '</strong></font></a>';
					}
					$dltext .= "</td></tr>\r\n";
				}
			}
		}
		mysql_free_result($result);
		if($dltext != ""){ ?>
            <table class="cobtbl" width="100%" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
			  <tr> 
                <td class="cobhl" align="center" colspan="3" bgcolor="#EBEBEB">
                  <strong><?php print $xxDlPros?></strong>
                </td>
              </tr>
			  <tr> 
                <td class="cobhl" align="left" bgcolor="#EBEBEB"><?php print $xxPrId?></td>
				<td class="cobhl" align="left" bgcolor="#EBEBEB"><?php print $xxPrNm?></td>
				<td class="cobhl" align="center" bgcolor="#EBEBEB"><?php print $xxDownl?></td>
			  </tr>
<?php		print $dltext; ?>
			</table>
	  <br />
<?php	}elseif(@$warnexpireddownload==TRUE){ ?>
            <table class="cobtbl" width="100%" border="0" bordercolor="#B1B1B1" cellspacing="1" cellpadding="3" bgcolor="#B1B1B1">
			  <tr> 
                <td class="cobhl" align="center" colspan="3" bgcolor="#EBEBEB">
                  <strong><?php print $xxDlPros?></strong>
                </td>
              </tr>
			  <tr> 
                <td class="cobll" align="center">
				  &nbsp;<br />
				  There are no downloadable products associated with this order or the download time has expired.
				  <br />&nbsp;
				</td>
			  </tr>
			</table>
	  <br />
<?php	}
		if(! (@$noshowdigiordertext==TRUE)){
?>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
        <tr>
          <td width="100%">
            <table width="100%" border="0" cellspacing="3" cellpadding="3">
			  <tr> 
                <td width="100%">
				  <span name="printcontent" id="printcontent">
					<?php print str_replace(array("\r\n","\n"),array("<br />","<br />"),$orderText)?>
				  </span>
                </td>
			  </tr>
			  <tr> 
                <td width="100%" align="center"><br />
<?php				if(trim($xxRecEml)!='')print $xxRecEml . '<br /><br />';
					print imageorbutton(@$imgcontinueshopping, '&nbsp;'.$xxCntShp.'&nbsp;', $storeurl, FALSE).'&nbsp;';
					print imageorbutton(@$imgprintversion, '&nbsp;'.$xxPrint.'&nbsp;', 'doprintcontent()', TRUE);
?><br/>				<img src="images/clearpixel.gif" width="300" height="3" alt="" />
                </td>
			  </tr>
			</table>
		  </td>
        </tr>
      </table>
<?php	}
	} ?>
