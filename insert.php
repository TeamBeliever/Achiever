<?php
include 'db.php';

$id     = $_POST['id'];
$name   = $_POST['full_name'];
$email  = $_POST['email'];
$mobile = $_POST['mobile'];
$gender = $_POST['gender'];

if(!empty($_FILES['photo']['name'])){
    $photo = $_FILES['photo']['name'];
    move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/".$photo);
} else {
    $photo = $_POST['old_photo'];
}

$sql = "UPDATE users SET
        full_name='$name',
        email='$email',
        mobile='$mobile',
        gender='$gender',
        photo='$photo'
        WHERE id=$id";

mysqli_query($conn, $sql);

/* ✅ ADD HEADER HERE */
header("Location: index.php?status=update");
exit;
