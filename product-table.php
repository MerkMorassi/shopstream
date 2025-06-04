<!DOCTYPE html>
<html>
<head>

    <title>Product Table</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	<link rel="stylesheet" href="css/themes/jquery.mobile.icons.min.css" />
    <meta name="robots" content="no index,no follow" />
	<meta http-Equiv="Cache-Control" Content="no-cache">
	<meta http-Equiv="Pragma" Content="no-cache">
	<meta http-Equiv="Expires" Content="0">
    <link rel="stylesheet" href="css/themes/shopstream-dark.css" />
    <link rel="stylesheet" href="css/sidebar-overlay.css" />

</head>

<body>

<div data-role="page" id="container" style="background-color: #000000;">
    
    <div data-role="header" data-position="fixed">
    
        <div data-role="navbar">
            
            <ul>
                <li><a href="product-table.php" data-icon="home"></a></li>
                <li><a href="clientlogin.php" data-icon="user" data-prefetch></a></li>
                <li><a href="preview.php" data-icon="bars" data-prefetch> </a></li>
                <li><a href="chat-example.html" data-icon="comment" data-prefetch></a></li>
                <li><a href="cart.php" data-icon="shop" data-prefetch></a></li>
            </ul>
         </div>
	
    </div>
    
    <script src='js/listener.js'></script>
    
    <?php
	include "db-checker.php"; 
	include "countdown-timer.php";
    // Everything Active Show Product & Countdown
    if($active > 0 && $stock > 1 && $display == 1 && $timestart < $now && $timeend > $now)   
    {?>
    
    <div data-role="none" id="product">
    
	<div class="ui-grid-a" id="timer">
        
        <!-- Stock Event Listener -->
        <div class="ui-block-a" style="width:33%;text-align:left;">
            <div class="ui-bar ui-bar-a">
            	<h1 id="stock" style="font-size:125%;"></h1>
            </div>
        </div>
        <!-- Stock Event Listener -->
        
        <div class="ui-block-b" style="width:34%; text-align:center;">
            <div class="ui-bar ui-bar-a">
            	<h1 style="font-size:125%;">$<?php echo $price;?></h1>
            </div>
        </div>
        
        <div class="ui-block-c" style="width:33%;text-align:right;" id="countdown-timer">
            <div class="ui-bar ui-bar-a">
            	<h1 style="font-size:125%;" id="countdown"></h1>
            </div>
        </div>
    </div>       
    
    <div class="ui-grid-a" id="product-details">
        
        <div class="ui-block-a" style="width:33%;">
            <div class="ui-bar ui-bar-a">
            <p><img src="<?php echo $image;?>" style="height="100%" width="100%"></p>
            </div>
        </div>
        
        <div class="ui-block-b" style="width:67%;">
            <div class="ui-bar ui-bar-a">
                <p><?php echo $name;?></p>
                <p><?php echo $description;?></p>
            </div>
        </div>
    </div>
    <!-- Form Buy Button -->
    <div data-role="none" class="ui-content">    
        <form method="post" action="cart.php" data-ajax=”false”>        
            <input type="hidden" name="id" value="<?php echo $pid;?>">
            <input type="hidden" name="mode" value="add">
            <input type="submit" value="BUY NOW" class="ui-btn" id="buybutton">
        </form>
	</div>
    
    <?php } else { ?>

		<div data-role="none" class="ui-content" style="background-color:#FFFFFF;" id="inactive">            
			
            <div data-role="none" class="ui-content">
				<h4>Nothing Active</h4>
			</div>
		
        </div>            
                    
    <?php } ?>
    
</div>

</body>
</html>
