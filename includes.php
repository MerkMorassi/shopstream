<?php
// Default Parameters ECTv6.23
$storename='www.shopstream.tv';
$sortBy = 1;
//$pathtossl = "";
$taxShipping=0;
$pagebarattop=1;
$productcolumns=1;
$useproductbodyformat=2;
$usesearchbodyformat=1;
$usedetailbodyformat=1;
$useemailfriend=FALSE;
$nobuyorcheckout=FALSE;
$noprice=FALSE;
$expireaffiliate=30;
$allproductsimage="";
$showtaxinclusive=FALSE;
$upspickuptype="03";
$overridecurrency=FALSE;
$orcsymbol=" AU\$";
$orcemailsymbol=" AU\$";
$orcdecplaces=2;
$orcdecimals=".";
$orcthousands=",";
$orcpreamount=TRUE;
$encryptmethod="";
$showcategories=FALSE;
$termsandconditions=FALSE;
$nomarkup=TRUE;
$usecsslayout=FALSE;
$loyaltypoints=100;
$loyaltypointvalue=0.0001;
$loyaltypointsnowholesale=TRUE;
$loyaltypointsnopercentdiscount=TRUE;
// Tweaks
$nochecksslserver=TRUE;
$usefirstlastname=TRUE;
$digidownloads=TRUE;
$digidownloadmethod="filesystem";
$digidownloadpattern="var/www/vhosts/shopstream.tv/private/downloads/"; 
$digidownloadwarn=TRUE;//warn and log IP if failed attempt
$digidownloademail="Your products can be downloaded for the next 3 days by going to the URL%nl%http://www.shopstream.tv/latedownload.php?ordid=%orderid%&pass=%password%%nl%Or alternatively, go to the URL%nl%http://www.shopstream.tv/latedownload.php%nl%. . . and enter the order ID %orderid% and password %password%%nl%%nl%";
$digidownloaddays=3;// number days for late download
//$minwholesaleamount=100.00;
//$minwholesalemessage="Wholesale Orders Require a $100 minimum";
//$nowholesalediscounts=FALSE;
//$clientloginref="my-account.php";//set per login level forwarding pages for the client login system or send the logged in client back to the referring page
//$clientloginref2="otherpage.php";//forwarding page for only those logged in with login level 2
$enableclientlogin=FALSE;
$forceclientlogin=FALSE;
$allowclientregistration=FALSE;//enable clients to self register
$noclientloginprompt=TRUE;//Remove login prompt
$noremember=TRUE;
$defaultcommission=10;
$showquantonproduct=FALSE;
$showquantondetail=FALSE;
$showinstock=TRUE;
$imgcheckoutbutton="button";
$noshowoutofstock=TRUE;
$nomarkup=TRUE;
$useimageincart=FALSE;
$linkcartproducts=FALSE;
$catseparator="<br>&nbsp;";
$categorycolumns=1;
$usecategoryformat=3;
$usecsslayout=FALSE;
$mobilebrowsercolumns=1;
$allproductsimage="";
$nobuyorcheckout=FALSE;
$shortdescriptionlimit=1000;
$nogiftcertificate=TRUE;
$autobillingtoshipping=TRUE;
$noprice=FALSE;
$noproductoptions=TRUE;
$noadditionalinfo=TRUE;
$noproductseparator=TRUE;
$prodseparator="<p align=\"center\">&nbsp;</p>";
$noshipaddress=FALSE;
$pricezeromessage="Call For Details";
$showproductid=FALSE;
$currencyseparator=" ";
$noproductoptions=TRUE; //options on products page
$dumpccnumber=TRUE;
$actionaftercart=2; //1, 2 Categories, 3, 4 skips added to cart
$cartrefreshseconds=2;
$allowemaildefaulton=TRUE;
$nomailinglist=TRUE; // remove promo signup from cart
$emailorderstatus=3;
$htmlemails=TRUE;
$alwaysemailstatus=TRUE;
$trackingnumtext="This is your tracking number: %s ";//%nl
$maintablebg="";
$innertablebg="";
$maintablewidth="100%";
$innertablewidth="100%";
$maintablespacing="0";
$innertablespacing="0";
$maintablepadding="0";
$innertablepadding="0";
$headeralign="left";
$dropshipheader="<br>general drop ship header<br>";
$dropshipfooter="general drop ship footer<br>";
$noshowdiscounts=FALSE;
$willpickuptext="";
$willpickupcost=0;
//$extraorderfield1="";
//$extraorderfield1required=FALSE;
//$extraorderfield2="";
$emailstyle2="font-size: 14px; font-family:Arial, Helvetica, sans-serif; color:#666666;"; // Font Style for the Products Purchased
$emailstyle="font-size: 14px; font-family:Arial, Helvetica, sans-serif; color:#666666;"; // Font Style for the Email Confirmation
$emailcolour="#f0f0f0"; // Background Colour of Title Header
$emailtxtbot="<span style='font-size:15px;'><strong>Thank you for ordering from " . $storename . "!</strong></span><br>"; 
$emailtxttop="<span style='font-size:15px;'><strong>Thank you for ordering from " . $storename . "!</strong></span><br>"; 
$emailfooter="<br>Thank you for shopping with us.<br><br>Kind Regards,<br>" . $packingslipaddress . ""; // Bottom Text of Order Confirmation Email
$emailstatustext="Dear %ordername%, <br><br>We are informing you that your order (ID %orderid%) has been updated from %oldstatus% to %newstatus% on %date%. Your tracking number is %trackingnum%"; //  Message that will appear in Order Status Update Email
$emailstattop="<span style='font-size:15px;'><strong>Thank you once again for ordering from " . $storename . "!</strong></span><br>"; // Order Status Update Header Text
$orderstatusemail="<table width='600' border='0' cellspacing='0' cellpadding='0' style='border:1px solid #e5e5e5;'>
<tr><td height='64' bgcolor='" . $emailcolour . "' style='padding:8px;'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
<tr><td><span style='font-size: 22px; font-family:Arial, Helvetica, sans-serif; color:#ffffff;'><strong> " . $storename . " </strong></span> </td>
<td align='right'><span style='font-size: 14px; font-family:Arial, Helvetica, sans-serif; color:#ffffff;'><strong>Order Status Update </strong></span></td>
</tr> </table></td>  </tr> <tr> <td ><table width='100%' border='0' cellspacing='0' cellpadding='0'>   <tr>  <td style='padding:9px; font-size: 11px; font-family:Arial, Helvetica, sans-serif; color:#666666; border-bottom:1px solid #e5e5e5;'>
" . $emailstattop . "</td></tr><tr><td style='padding:9px; font-size: 11px; font-family:Arial, Helvetica, sans-serif; color:#666666;'>
" . $emailstatustext . "</td></tr><tr>  <td style='padding:9px; font-size: 11px; font-family:Arial, Helvetica, sans-serif; color:#666666;'>" . $emailtxtbot . " </td></tr></table></td></tr></table> ";
// ===================================================================
// Please do not edit anything below this line
// ===================================================================

error_reporting (E_ALL ^ E_NOTICE);

define("maxprodopts",15);
define("helpbaseurl","http://www.ecommercetemplates.com/phphelp/ecommplus/");
?>
