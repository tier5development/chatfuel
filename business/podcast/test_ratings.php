<!DOCTYPE HTML>  
<html>
<head>
</head>
<body>  
<h1 align="centre">Rate us!</h1>


<?php

//$server='localhost';
/*$dbname='chatfuel';
$userdb='root';
$passdb='toor';
$tname='webview';

$remarks = $_POST["remarks"];
$comment =$_POST["comment"];

//session_start();
//$conn= new mysqli($servername,$userdb,$passdb,$dbname);

/*if($conn->connect_error){
	die("Connection failed : ".$conn->connect_error);
	}
$sql = ("INSERT INTO ".$tname." (remarks,comment) VALUES ('".$remarks."','".$comment."')");

if($conn->query($sql)===TRUE)
{
   // echo "New Record Created succussfully";
}
else
{
    echo "Error : ".$sql."<br>".$conn->error;
}
*/

$ratings = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $ratings = ($_POST["ratings"]);
  //$comment = ($_POST["comment"]);
}

?>


<script>
      // Code copied from Facebook to load and initialise Messenger extensions
      (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.com/en_US/messenger.Extensions.js";
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'Messenger'));
    </script>
<form method="post" action="thanks.html">
<br><br>
 <input type="radio" name="ratings" <?php if (isset($ratings) && $ratings=="good") echo "checked";?> value="like">Was good!
 <br><br>
<input type="radio" name="ratings" <?php if (isset($ratings) && $ratings=="fun") echo "checked";?> value="dislike">Was fun!
 <br><br>
<input type="radio" name="ratings" <?php if (isset($ratings) && $ratings=="excellent") echo "checked";?> value="dislike">Was excellent!
<br><br>
<button name="submit" action="submit">Submit</button>
<br><br>
</form>

</body>
</html>
