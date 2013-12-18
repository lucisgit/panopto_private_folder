<?php
/*
*   main processing file.  reads confirmation from form and user details from session.
*/
    session_start();
    if(!isset($_POST['confirm']) || !isset($_SESSION['userDetails'])){
        header("Location : index.php?response=-1");//no confirmation var sent or no userdetails set in session.
        exit;
    }else if($_POST['confirm']!="1"){
        header("Location : index.php?response=-2");//no confirmation ticked
        exit;
    }
    require_once("lib/mod_panopto_data.php");
    //printf ("realm is '%s'<br>", $_SERVER["REMOTE_REALM"]);
    //printf ("user is '%s'<br>", $_SERVER["REMOTE_USER"]);

    $userDetails = $_SESSION['userDetails'];
  
    //echo var_dump($userDetails);

    //echo "</br></br>";
    //$_SERVER["REMOTE_USER"]
    $panopto_data = new mod_panopto_data($userDetails);

    //var_dump($panopto_data);
    //provision the course
    $provisioning_data = $panopto_data->get_provisioning_info();
    //echo "</br></br>";
    //var_dump($provisioning_data);
    $provisioned_data  = $panopto_data->provision_course($provisioning_data);
    //echo "</br></br>";
    //var_dump($provisioned_data);
    //echo "</br></br>";

    if($provisioned_data==NULL){
        header("Location : index.php?response=-3");//error
        exit;
    }

    header("Location : index.php?response=1");//success
    exit;
    
    