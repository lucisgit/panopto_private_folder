<?php

    require_once("lib/mod_panopto_data.php");
    require_once("templates/template.php");
    session_start();
    $response = 0;//default response is 0
    if(isset($_GET['response'])){//if response is sent, get it
        $response = $_GET['response'];
    }

    //LDAP lookup on logged in user
    $username= $_SERVER["REMOTE_USER"];
    $hostname = "ldap.lancs.ac.uk";
    $base_dn = "dc=lancs,dc=ac,dc=uk";
    $bind_dn = "cn=iss_panopto_prv_svc,ou=ISS,ou=Services,ou=Users,dc=lancs,dc=ac,dc=uk ";
    $bind_pass = "good-rant-tact";

    // connect to ldap server
    $ds = ldap_connect($hostname);

    // bind to ldap server
    $ldapbind = @ldap_bind($ds, $bind_dn, $bind_pass);

    // verify binding
    if (!$ldapbind){
        header("Location : index.php?response=-3");//LDAP bind failed
        exit;
    }

    // do the search
    $sr=ldap_search($ds, $base_dn, "uid=$username");

    // get the results
    $entries = ldap_get_entries($ds, $sr);

    if ($entries["count"] == 0) {//user not found
        header("Location : index.php?response=-3");//error
        exit;
    }
    if ($entries["count"] > 1) {//more than 1 result
        header("Location : index.php?response=-3");//error
        exit;
    }

    //create userDetails class
    $userDetails = new StdClass();
    $userDetails->firstName = $entries[0]["givenname"][0];
    $userDetails->lastName = $entries[0]["sn"][0];
    $userDetails->email = $entries[0]["mail"][0];
    $userDetails->userName = $entries[0]["uid"][0];
    //save to session
    $_SESSION['userDetails'] = $userDetails;

    //display correct form
    switch($response){
        case 0://standard
            display_form_standard($userDetails);
            break;
        case -1://error occured - no confirmation var sent
            display_form_standard($userDetails);
            break;
        case -2://error occured - no confirmation ticked
            display_form_confirm($userDetails);
            break;
        case -3:
            display_form_error($userDetails);
            break;
        case 1:
            display_success($userDetails);
            break;
        default:
            display_form_standard($userDetails);
    }
    

?>











    
    