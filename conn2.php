<?php

session_start();
include 'conn.php';
/*ini_set('display_errors', 1);
error_reporting(E_ALL);*/

if($_POST) {
    $mes_id = $_POST['messenger_user_id'];
    $feedback1 = $_POST['fdb1'];
    $feedback2 = $_POST['fdb2'];
        $inf = $_POST['inf'];

        $last_id="";

}

$last_id=$con->insert_id;

$sql1 = "INSERT INTO `level_1_videos` (`messenger_id`,`feedback1`,`feedback2`,`influencer`) VALUES ('$mes_id','$feedback1','$feedback2','$inf')";

if (mysqli_query($con, $sql1)) {
         $_SESSION["id"]=$last_id;

        $user_detail = new stdClass();
        $user_detail ->type="text";
        $user_detail="Feedback saved successfully";
        $list_view= new stdClass();
        $list_view->messages[] = ['text' => $user_detail];

  } else {
	 $user_detail = new stdClass();
         $user_detail ->type="text";
         $user_detail="Try again";
         $list_view= new stdClass();
         $list_view->messages[] = ['text' => $user_detail]; 

  }

  echo json_encode($list_view);

?>

