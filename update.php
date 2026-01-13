<?php
include 'db.php';

$id        = $_POST['id'];
$name      = $_POST['full_name'];
$email     = $_POST['email'];
$mobile    = $_POST['mobile'];
$gender    = $_POST['gender'];
$old_photo = $_POST['old_photo'];

/* Photo update check */
if (!empty($_FILES['photo']['name'])) {
    $photo = $_FILES['photo']['name'];
    $tmp   = $_FILES['photo']['tmp_name'];

    move_uploaded_file($tmp, "uploads/" . $photo);

    // old photo delete (optional but clean)
    if (file_exists("uploads/" . $old_photo)) {
        unlink("uploads/" . $old_photo);
    }
} else {
    $photo = $old_photo; // agar new photo nahi select ki
}

/* Update query */
$query = "UPDATE users SET 
            full_name = '$name',
            email     = '$email',
            mobile    = '$mobile',
            gender    = '$gender',
            photo     = '$photo'
          WHERE id = $id";

mysqli_query($conn, $query);

header("Location: index.php");
exit;
?>
