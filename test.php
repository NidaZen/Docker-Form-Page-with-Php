<?php
 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("config.php");
if (isset($_POST['submitbutton']))  // ilk kurulum
{
    $d_name = $_POST['dname'];
   
    $i_name = $_POST['iname'];

    if ($i_name == "") 
    $i_name = $_POST['taskOption'];

    $versiyon = $_POST['versiyon'];
    $volume = $_POST['volume'];
    $dıs_port = $_POST['dıs_port'];
    $ic_port = $_POST['ic_port'];
   

    $kalıp = "sudo docker run -d -t ";
    $port= "";
    $name = "";
    $vol = "";
    $vers = "";

    if($dıs_port != "" && $ic_port != "") {
      $port = "-p {$dıs_port}:{$ic_port}"; 
    }
    
    if($d_name != "") {
      $name = "--name {$d_name}";
    }

    if($volume != "") {
      $vol = "-v {$volume}";
    }
    else{
      $vol = "";
    }

    $imaj= "{$i_name}" ;

    if($versiyon != "") {
      $vers = ":{$versiyon}";
    }
    else {
      $vers ="";
    }
    $output=shell_exec("$kalıp $port $name $vol $imaj$vers");

    //veri ekleme
    $sql = "INSERT INTO dockerlar (docker_ismi, imaj_ismi, versiyon, volume, dıs_port, ic_port , durum) 
    VALUES ('$d_name', '$i_name', '$versiyon' , '$volume' , '$dıs_port' , '$ic_port' , 'çalışıyor')";

    $sonuc= $baglanti->query($sql); 
    
}

else if (isset($_POST['durdur_button'])) {
  $name = $_POST['docker_ismi'];
  $sorgu=$baglanti->prepare("UPDATE dockerlar SET durum='exited' WHERE docker_ismi ='$name'");
	$sonuc=$sorgu->execute();
  $output=shell_exec("sudo docker stop {$name}");
}


else if (isset($_POST['calistir_button'])) {
  $name = $_POST['docker_ismi'];
  $sorgu=$baglanti->prepare("UPDATE dockerlar SET durum='running' WHERE docker_ismi ='$name'");
	$sonuc=$sorgu->execute();
  $output=shell_exec("sudo docker start {$name}");
}

else if (isset($_POST['sil_button'])) {
  $name = $_POST['docker_ismi'];
  $date=date("Y-m-d");
  $sorgu=$baglanti->prepare("UPDATE dockerlar SET deleted_at= '$date' , durum='silindi' WHERE docker_ismi ='$name'");
	$sonuc=$sorgu->execute();
  $output=shell_exec("sudo docker stop {$name}");
  $output=shell_exec("sudo docker rm {$name}");


}

  $db = $baglanti->query("SELECT * FROM dockerlar WHERE  deleted_at IS NULL");
  $rows = $db->fetchAll(PDO::FETCH_ASSOC);

?>

<html>
<head>
   <!-- Required meta tags -->
   <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


 <!--<link href="style.css" rel="stylesheet">  --->
<link href="bootstrap.css" rel="stylesheet"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"


href="index.css"> 
<title>Docker Form</title>


</head>

<body>
<div class="form" style= "height:63%">
    <h2 align="center" style="  background:#5b7ea0; height:40px; color:white;" >
    DOCKER FORM
    </h2>

    <table class="center"  >
        <form action="#" method="post">

  <tr>
    <th>Docker Adı</th>
    <td><input type="text" name="dname" placeholder="KCRedis" /> </td>
  </tr>
  <tr>
    <th>İmaj Adı</th>

    <td><div class="form-group">
	
    <select id="exampleFormControlSelect1" name="taskOption">
        <option>---Seçiniz---</option>
        <option>php</option>
        <option>nginx</option>
        <option>node</option>
        <option>ubuntu</option>
        <option>mysql</option>
    </select>

    ya da yazınız
    <input type = "text" id="textvalue" name= "iname"/>

  </div>

  </tr>

  <tr>
    <th>Versiyonu</th>
    <td><input type="text" name="versiyon" placeholder="3" /></td>
  </tr>

  <tr>
    <th>Volume</th>
    <td><input type="text" name="volume" placeholder=""/></td>
  </tr>

  <tr>
    <th>Port</th>
    <td>
      dış port:
    <input type="text" name="dıs_port" placeholder="1200" style=" width: 340px" /> 
    <br>

    iç port:
    <input type="text" name="ic_port" placeholder="80" style=" width: 348px" /></td>
  </tr>

  <tr>
    <th>Spesifik Veri</th>
    <td><input type="text" name="spesifikveri"/> </td>
  </tr>

  
</table>

<p><input id= "gonder" type="submit" name="submitbutton" class= "buton"/></p>
</form>
</div>

<div  class= "liste"  >
    <div class= "baslik"> 
    <h2 align="center" style="  background:#5b7ea1; color: white; height:40px;" > 
  
    DOCKER LİSTESİ
    </h2>
    </div>
    <table class= "tableliste" cellpadding="10px">
    <tr>
    <th>id</th>
    <th>Docker İsmi</th>
    <th>İmaj İsmi</th>
    <th>Versiyon</th>
    <th>Volume</th>
    <th>Dış Port</th>
    <th>İç Port</th>
    <th>Durumu</th>
    <th>Oluşturulma Tarihi</th>
    <th>Güncelleme Tarihi</th>
    <th>Docker ID</th>
    <th>Fonksiyon</th>
  </tr>
      <?php
        foreach($rows as $veri) {
        $veri = (object) $veri;
      ?>
      <tr>
       <td><?=$veri->id ?></td>
       <td><?=$veri->docker_ismi ?></td>  
       <td><?=$veri->imaj_ismi ?></td>  
       <td><?=$veri->versiyon  ?></td>  
       <td><?=$veri->volume  ?></td> 
       <td><?=$veri->dıs_port  ?></td>
       <td><?=$veri->ic_port  ?></td>    
       <td><?=shell_exec("sudo docker inspect -f '{{.State.Status}}' $veri->docker_ismi ")?></td>    
       <td><?=$veri->created_at ?></td> 
       <td><?=$veri->updated_at ?></td>  
       <td><?=shell_exec("sudo docker ps -aqf 'name= $veri->docker_ismi'")?></td> 
       <td>
        <form method="post">

        <?php 
        $status = shell_exec("sudo docker inspect -f '{{.State.Status}}' $veri->docker_ismi ");
        $status0 = "exited"; 
        $status1 = "running";
      if(strcmp($status, $status0) == 1) {
        ?>
        
        <button type="submit" name="calistir_button" class="btn btn-success btn-sm"><i class="fa fa-play"></i> Çalıştır</button>
        <?php 
      }
       
      else if(strcmp($status, $status1) == 1) {
        ?>
        <button type="submit" name="durdur_button" class="btn btn-warning btn-sm"><i class="fa fa-stop"></i> Durdur</button>
        <?php 
      }
      ?>
        
          
          <a class="btn btn-primary btn-sm" href="log.php?id=<?= $veri->docker_ismi ?>" role="button"><i class="fa fa-file"></i> Log</a>
          <button type="submit" name="sil_button" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Sil</button>
          <input type="hidden" name="id" value="<?=$veri->id ?>">
          <input type="hidden" name="docker_ismi" value="<?=$veri->docker_ismi?>">
        </form>
      </td>
    </tr>
    <?php 
    }
    ?>
    </table>
    
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
