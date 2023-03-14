<?php
require_once(__DIR__.'/../../../../config.php');
use block_imagenav\lib;
$lib = new lib;

if(isset($_POST['total']) && isset($_POST['imagewidth']) && isset($_POST['imageheight']) && isset($_POST['role'])){
    $error = false;
    $errors = [];
    $total = $_POST['total'];
    $imagewidth = $_POST['imagewidth'];
    $imageheight = $_POST['imageheight'];
    $aspectratio = $_POST['aspectratio'];
    $role = $_POST['role'];
    if($_POST['total'] < 1 || empty($_POST['total'])){
        $errors['total'] = true;
        $error = true;
    }
    if($imagewidth < 1 || empty($imagewidth)){
        $errors['imagewidth'] = true;
        $error = true;
    }
    if($imageheight < 1 || empty($imageheight)){
        $errors['imageheight'] = true;
        $error = true;
    }
    if($aspectratio != 'aspectratio' && !empty($aspectratio)){
        $errors['aspectratio'] = true;
        $error = true;
    }
    if($role === 'admin'){
        $role = 'admin';
    } elseif($role === 'coach'){
        $role = 'coach';
    } elseif($role === 'learner'){
        $role = 'learner';
    } else{
        $errors['role'] = true;
        $error = true;
    }
    if($errors['total'] != true){
        $total = $_POST['total'];
    }
    $files = [];
    $links = [];  
    if($total > 0){
        for($i = 1; $i <= $total; $i++){
            $file = $_FILES['file'.$i];
            if(end(explode(".", $file['name'])) != 'png' && end(explode(".", $file['name'])) != 'jpg' && end(explode(".", $file['name'])) != 'jpeg' || $file['type'] != "image/jpg" && $file['type'] != 'image/png' && $file['type'] != 'image/jpeg'){
                $errors['file'][$i - 1] = true;
                $error = true;
            } else {
                $path = $file['tmp_name'];
                $type = end(explode(".",$file['name']));
                $data = file_get_contents($path);
                $base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
                $files[$i-1] = $base64;
            }
            $link = $_POST['link'.$i];
            if(!filter_var($link, FILTER_VALIDATE_URL)){
                $errors['link'][$i - 1] = true;
                $error = true;
            } else {
                $links[$i-1] = $_POST['link'.$i];
            }
        }
    }
    if($error === true){
        echo("error");
    } else if($error === false) {
        echo("success");
        $lib->setup_nav_config($total, $imagewidth, $imageheight, $aspectratio, $role, $files, $links);
        header("Location: ./../../configuration.php");
    }
}