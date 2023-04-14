<?php
require_once(__DIR__.'/../../../../config.php');
use block_imagenav\lib;
$lib = new lib;

if(isset($_POST['role']) && isset($_POST['id'])){
    $id = $_POST['id'];
    $role = $_POST['role'];
    $roleTypes = ['admin','learner','coach'];
    $error = false;
    $errorText = new stdClass();
    if(!in_array($role, $roleTypes) || empty($role)){
        $error = true;
        $errorText->role = true;
    }  
    if(!preg_match("/^[0-9]*$/", $id) || empty($id)){
        $error = true;
        $errorText->id = true;
    }
    if($error){
        
    } else {
        echo(json_encode($errorText));
    }
}