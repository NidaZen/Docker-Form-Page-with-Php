<?php

 $baglanti = new PDO("mysql:host=10.14.5.237; dbname=docker_form; port=3307", 'zenginoglu', '64326432');
 $baglanti->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 // Check connection 
 if (!$baglanti) {
 die("Connection failed: " . mysqli_connect_error());
 }

 ?>
