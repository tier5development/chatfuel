<!DOCTYPE HTML>  
<html>
<head>
</head>
<body>  

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

$remarks = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $remarks = ($_POST["remarks"]);
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

<iframe width="350" height="315"
src="https://www.youtube.com/embed/wHGqp8lz36c">
</iframe>
<br><br>
<form method="post">
Remarks:<br><br>
 <input type="image" src="Likes.jpg" name="remarks" height="100" width="250" formaction="test_ratings.php" <?php if (isset($remarks) && $remarks=="like") echo "checked";?> value="like">
 <br>
<input type="image" src="facebook-dislike-button.jpg" name="remarks" height="100" width="250" formaction="sorry.html" <?php if (isset($remarks) && $remarks=="dislike") echo "checked";?> value="dislike">
<br><br>
<br><br>
</form>


</body>
</html>

