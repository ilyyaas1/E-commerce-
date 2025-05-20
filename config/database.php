<?php

function connectMabasi(){
    $lien = new mysqli('localhost','root','Bennani!ilyass?*','site_ecom');
    if($lien->connect_error){
        die("Connection failed: ". $lien->connect_error );
    }
    return $lien;
}
