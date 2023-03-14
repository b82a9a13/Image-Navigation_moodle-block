<?php
require_once(__DIR__.'/../../../../config.php');
use block_imagenav\lib;
$lib = new lib;
if((isset($_POST['fileTotal']) || isset($_POST['linkTotal']) || isset($_POST['width']) || isset($_POST['height']) || isset($_POST['aspectratio'])) && isset($_POST['role'])){
    $errors = [];
    $error = false;
    $files = [];
    $fileTotal = $_POST['fileTotal'];
    $errorpos = 0;
    $correctpos = 0;
    if($fileTotal > 0){
        for($i = 0; $i < $fileTotal; $i++){
            $file = $_FILES['file'.$i];
            $id = $_POST['file'.$i.'id'];
            if(end(explode(".", $file['name'])) != 'png' && end(explode(".", $file['name'])) != 'jpg' && end(explode(".", $file['name'])) != 'jpeg' || $file['type'] != 'image/jpg' && $file['type'] != 'image/png' && $file['type'] != 'image/jpeg'){
                $errors['file'][$errorpos] = $id;
                $error = true;
                $errorpos++;
            } else {
                $path = $file['tmp_name'];
                $type = end(explode(".", $file['name']));
                $data = file_get_contents($path);
                $base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
                $files[$correctpos] = [$id, $base64];
                $correctpos++;
            }
        }
    }
    $links = [];
    $errorpos = 0;
    $correctpos = 0;
    $linkTotal = $_POST['linkTotal'];
    if($linkTotal > 0){
        for($i = 0; $i < $linkTotal; $i++){
            $link = $_POST['link'.$i];
            $id = $_POST['link'.$i.'id'];
            if(!filter_var($link, FILTER_VALIDATE_URL)){
                $errors['link'][$errorpos] = $id;
                $error = true;
                $errorpos++;
            } else {
                $links[$correctpos] = [$id, $link];
                $correctpos++;
            }
        }
    }
    $width = $_POST['width'];
    if(!$width > 0 && isset($_POST['width'])){
        $errors['width'] = true;
        $error = true;
    }
    $height = $_POST['height'];
    if(!$height > 0 && isset($_POST['height'])){
        $errors['height'] = true;
        $error = true;
    }
    $aspectratio = $_POST['aspectratio'];
    if($aspectratio != 'true' && $aspectratio != 'false' && isset($_POST['aspectratio'])){
        $errors['aspectratio'] = true;
        $error = true;
    }
    $role = $_POST['role'];
    if($role != 'admin' && $role != 'learner' && $role != 'coach'){
        $errors['role'] = true;
        $error = true;
    }
    if($error === false){
        $lib->update_nav_config($width, $height, $aspectratio, $role, $files, $links);
        $success['success'] = true;
        echo(json_encode($success));
    } elseif($error === true){
        print_r(json_encode($errors));
    }
}