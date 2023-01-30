<?php
echo "";
if(isset($_GET["id"])) {
include("config.php");
  $output= shell_exec("sudo docker logs ".$_GET["id"]);
  echo "<pre>$output</pre>";
}
?>
