<SCRIPT language="php">
$today = date("l");
$now = date("H:i:s");
$opentime = "11:00:01";
$closetime = "20:00:01";
// What day is it?
if ($today == "Sunday")
{
$message = "<strong>SORRY - WE'RE CLOSED TODAY.</strong><br><br>";
print $message;
$nobuyorcheckout = "TRUE";
}
// If today is not Sunday, what time is it now and are we open or closed?
elseif ($opentime < $now && $now < $closetime)
{
$open = "<stong>Yes We're Open!</strong>";
print $open;
}
else
{
$closed = "<strong>Sorry - We're Closed.</strong><br>Online Ordering Hours:<br>Monday-Saturday<br>11 am - 8 pm EST<br>";
$nobuyorcheckout = "TRUE";
print $closed;
}
</script> 
