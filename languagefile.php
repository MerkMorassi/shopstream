<?php
//This code is copyright (c) Internet Business Solutions SL, all rights reserved.
//The contents of this file are protected under law as the intellectual property
//of Internet Business Solutions SL. Any use, reproduction, disclosure or copying
//of any kind without the express and written permission of Internet Business 
//Solutions SL is forbidden.
//Author: Vince Reid, vince@virtualred.net
$result = mysql_query("SELECT storelang FROM admin WHERE adminid=1") or print(mysql_error());

if($rs=mysql_fetch_assoc($result)){
	if(@$isvsadmindir) $dirpath=''; else $dirpath='/vsadmin';
	if($rs['storelang']=='de'){
		include '.' . $dirpath . '/inc/languagefile_de.php';
	}elseif($rs['storelang']=='dk'){
		include '.' . $dirpath . '/inc/languagefile_dk.php';
	}elseif($rs['storelang']=='es'){
		include '.' . $dirpath . '/inc/languagefile_es.php';
	}elseif($rs['storelang']=='fr'){
		include '.' . $dirpath . '/inc/languagefile_fr.php';
	}elseif($rs['storelang']=='it'){
		include '.' . $dirpath . '/inc/languagefile_it.php';
	}elseif($rs['storelang']=='nl'){
		include '.' . $dirpath . '/inc/languagefile_nl.php';
	}elseif($rs['storelang']=='pt'){
		include '.' . $dirpath . '/inc/languagefile_pt.php';
	}else
		include '.' . $dirpath . '/inc/languagefile_en.php';
}
mysql_free_result($result);
?>