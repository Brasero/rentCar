<?php
if(isset($_POST['marque'])){
    $imgData = $_FILES['img'];

    if($imgData['error'] == 0) {
        $path = dirname(__DIR__)."/public/assets/img/";
        $temp = $imgData['tmp_name'];
        $name = $imgData['name'];
        $path .= $name;
        move_uploaded_file($temp, $path);
        header('Location: ?page=home');
    } else {
        echo 'une erreur s\'est produite.';
    }

} else {
    require dirname(__DIR__) . '/view/addVehicule.php';
}