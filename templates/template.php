<?php

function display_style_headers(){
    ?>

    <div class="wide">
    <section id="main" role="main" class="clearfix">

    <h1>Panopto Private Folder Creation</h1>
    <?php
}

function display_style_footers(){
    //write page footer
}

//writes the main form for creating a private folder.
function display_form($userDetails, $confirmString = ""){
    ?>

    <p>Welcome to the Panopto Private Folder Creation page!</p>
    <p>This page will create you a private folder to store your videos on Panopto.  
        Please confirm the details below and press confirm when you are happy.  
        If you have any queries, please contact the ISS Service Desk</p>
    <p>Username: <?=$userDetails->userName?></p>
    <p>Email: <?=$userDetails->email?></p>
    <p>First Name: <?=$userDetails->firstName?></p>
    <p>Last Name: <?=$userDetails->lastName?></p>
    <p>By clicking Confirm below, you will create a folder on Panopto titled "Private Folder: <?=$userDetails->userName?>"</p>
    <form action="submit.php" method="post">
        <input type="hidden" name="confirm" value="0">
        <input type="checkbox" name="confirm" value="1">I Confirm the details above are correct <?=$confirmString?><br/>
        <input type="Submit" value="Confirm">
    </form>

    </section>
    </div>

    <?php
}
//The standard display of the form
function display_form_standard($userDetails){
    display_style_headers();
    display_form($userDetails);
    display_style_footers();
}
//Display the success message
function display_success($userDetails){
    display_style_headers();
    ?>
    
    <p>Thank you <?=$userDetails->firstName?>, your private folder "Private Folder: <?=$userDetails->userName?>" has been created!</p>
    <p>It can be found by logging into <a target="_blank" href="http://dtu-panopto.lancs.ac.uk/">Panopto</a></p>
    <p>If you have a previous private folder under a different name, it is advised that you copy your existing sessions across to this new folder.</p>


    <?php
    display_style_footers();
}
//Display the form with a prompt to confirm the details.  This happens when the check box is not checked
function display_form_confirm($userDetails){
    display_style_headers();
    
    echo "<p style=\"color:red;\">Please attend the areas in red</p>";
    $confirmString = "<span style=\"color: red;\">Please tick to confirm the details are correct</span>";
    
    display_form($userDetails,$confirmString);
}
//Display the form, but with an error message.  Generic message appears.  Problems should be reported to ISS Service Desk
function display_form_error($userDetails){
    display_style_headers();
    ?>
    <p style="color:red;">Unfortunately, an error occured attempting to create your new folder.  Please try again later or contact the ISS Service Desk</p>
    
    <?php
    display_form($userDetails);
    display_style_footers();
}
