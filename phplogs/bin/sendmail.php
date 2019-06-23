<?php

$to = "mehike@sms.emt.ee";
$subject = "My subject";
$txt = ''.$argv[1];;
echo $txt;
$headers = "From: logs@xglogs.emt.ee" . "\r\n" .
"CC: rynno.ruul@telia.ee";

mail($to,$subject,$txt,$headers);
?>
