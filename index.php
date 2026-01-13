<?php
include 'db.php';
session_start();

/* =====================
   DELETE (FIRST)
===================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $res = mysqli_query($conn,"SELECT photo FROM users WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['photo'])) {
            $file = "uploads/".$row['photo'];
            if (file_exists($file) && !is_dir($file)) {
                unlink($file);
            }
        }
    }

    mysqli_query($conn,"DELETE FROM users WHERE id=$id");

    $_SESSION['msg'] = "User deleted successfully!";
    $_SESSION['msgType'] = "success";
    header("Location:index.php");
    exit;
}

/* =====================
   INSERT / UPDATE
===================== */
if (isset($_POST['submit'])) {

    $name   = $_POST['full_name'];
    $email  = $_POST['email'];
    $mobile = $_POST['mobile'];
    $gender = $_POST['gender'];

    $allowedExt = ['jpg','jpeg','png'];

    /* ===== UPDATE ===== */
    if (!empty($_POST['id'])) {

        $id = (int)$_POST['id'];
        $old_photo = $_POST['old_photo'];
        $photo = $old_photo;

        if (!empty($_FILES['photo']['name'])) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExt)) {
                $_SESSION['msg'] = "Only JPG, JPEG, PNG allowed!";
                $_SESSION['msgType'] = "error";
                header("Location:index.php");
                exit;
            }

            $photo = time().'_'.$_FILES['photo']['name'];
            move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/$photo");

            if (!empty($old_photo) && file_exists("uploads/$old_photo")) {
                unlink("uploads/$old_photo");
            }
        }

        mysqli_query($conn,"UPDATE users SET
            full_name='$name',
            email='$email',
            mobile='$mobile',
            gender='$gender',
            photo='$photo'
            WHERE id=$id
        ");

        $_SESSION['msg'] = "User updated successfully!";
        $_SESSION['msgType'] = "success";
    }
    /* ===== INSERT ===== */
    else {

        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $_SESSION['msg'] = "Only JPG, JPEG, PNG allowed!";
            $_SESSION['msgType'] = "error";
            header("Location:index.php");
            exit;
        }

        $photo = time().'_'.$_FILES['photo']['name'];
        move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/$photo");

        mysqli_query($conn,"INSERT INTO users
        (full_name,email,mobile,gender,photo)
        VALUES
        ('$name','$email','$mobile','$gender','$photo')
        ");

        $_SESSION['msg'] = "User registered successfully!";
        $_SESSION['msgType'] = "success";
    }

    header("Location:index.php");
    exit;
}

/* =====================
   EDIT FETCH
===================== */
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn,"SELECT * FROM users WHERE id=$id");
    $editData = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>User Registration Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

<!-- Buttons extension CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<style>
*{
    box-sizing: border-box;
    font-family: "Segoe UI", Arial, sans-serif;
}

body{
    margin: 0;
    padding: 15px;
    background: #f4f6fb;
}

.container{
    max-width: 1100px;
    margin: auto;
}

.card{
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

h2{
    text-align: center;
    margin-bottom: 20px;
}

.form-grid{
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

@media (min-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr 1fr;
    }
    .form-grid .full {
        grid-column: 1 / 3;
    }
}

input,
select {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

button{
    background: #4f46e5;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    padding: 12px 40px;
    border-radius: 8px;
    border: 1px solid #ccc;
    display: block;
    margin: 20px auto 0;
}

@media (max-width: 576px) {
    button {
        width: 100%;
        padding: 14px;
    }
}

.table-wrapper {
    overflow-x: auto;
    margin-top: 30px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

table{
    width: 100%;
    border-collapse: collapse;
    min-width: 850px;
}

th,
td{
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

th{
    background: #4f46e5;
    color: #fff;
}

img{
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.action a{
    padding: 6px 10px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
    font-size: 13px;
}

.edit{
    background: #0d6efd;
}

.delete-btn {
    background: #dc3545;
    padding: 6px 10px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
    font-size: 13px;
    cursor: pointer;
    border: none;
}

.delete-btn:hover {
    opacity: 0.85;
}

@media (max-width: 576px) {
    body { padding: 10px; }
    .card { padding: 15px; }
    th, td { padding: 10px; font-size: 14px; }
    img { width: 42px; height: 42px; }
}

/* Optional: make export buttons look nicer */
.dt-buttons {
    margin-bottom: 15px;
}
.dt-button {
    background: #4f46e5 !important;
    color: white !important;
    border: none !important;
    margin-right: 8px !important;
    padding: 8px 14px !important;
    border-radius: 6px !important;
}
</style>

</head>

<body>

<div class="container">

<div class="card">
<h2><?= $editData ? 'Edit User' : 'Register User' ?></h2>

<form method="POST" enctype="multipart/form-data">
<?php if($editData){ ?>
<input type="hidden" name="id" value="<?= $editData['id']; ?>">
<input type="hidden" name="old_photo" value="<?= $editData['photo']; ?>">
<?php } ?>

<div class="form-grid">

<!-- Full Name -->
<div>
    <label for="full_name">Full Name</label>
    <input type="text" id="full_name" name="full_name" placeholder="Enter full name" 
    value="<?= $editData['full_name'] ?? '' ?>" required>
</div>

<!-- Email -->
<div>
    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="Enter email" 
    value="<?= $editData['email'] ?? '' ?>" required>
</div>

<!-- Mobile -->
<div>
    <label for="mobile">Mobile Number</label>
    <input type="text" id="mobile" name="mobile" placeholder="Enter mobile number" 
    value="<?= $editData['mobile'] ?? '' ?>" pattern="[0-9]{10}" maxlength="10" 
    oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
</div>

<!-- Gender -->
<div>
    <label for="gender">Gender</label>
    <select id="gender" name="gender" required>
        <option value="">Select Gender</option>
        <option value="Male" <?= ($editData && $editData['gender']=='Male')?'selected':''; ?>>Male</option>
        <option value="Female" <?= ($editData && $editData['gender']=='Female')?'selected':''; ?>>Female</option>
    </select>
</div>

<!-- Photo -->
<div class="full">
    <label for="photo"><?= $editData ? 'Change Photo' : 'Upload Photo' ?></label>
    <input type="file" id="photo" name="photo" <?= $editData ? '' : 'required'; ?>>
    <small style="color:#666;">Only JPG, JPEG, PNG files allowed (Max size: 5MB)</small>
    <?php if($editData){ ?><br><img src="uploads/<?= $editData['photo']; ?>" alt="User Photo"><?php } ?>
</div>

<!-- Submit Button -->
<div class="full" style="text-align:center;">
    <button type="submit" name="submit"><?= $editData ? 'Update' : 'Register'; ?></button>
</div>

</div>
</form>
</div>

<div class="table-wrapper">
<table id="userTable">
<thead>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Gender</th><th>Photo</th><th>Action</th>
</tr>
</thead>
<tbody>
<?php
$res = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");
while($row=mysqli_fetch_assoc($res)){
?>
<tr>
<td><?= $row['id']; ?></td>
<td><?= $row['full_name']; ?></td>
<td><?= $row['email']; ?></td>
<td><?= $row['mobile']; ?></td>
<td><?= $row['gender']; ?></td>
<td>
  <img src="uploads/<?= $row['photo']; ?>" 
       alt="User Photo" 
       style="cursor:pointer;" 
       onclick="showPhoto('uploads/<?= $row['photo']; ?>')">
</td>

<td class="action">
    <a class="edit" href="index.php?edit=<?= $row['id']; ?>">Edit</a>
    <a href="javascript:void(0);" class="delete-btn" data-id="<?= $row['id']; ?>">Delete</a>
</td>

</tr>
<?php } ?>
</tbody>
</table>
</div>

</div>

<?php
if(isset($_SESSION['msg'])){
?>
<script>
Swal.fire({
icon:"<?= $_SESSION['msgType'] ?>",
title:"<?= $_SESSION['msg'] ?>",
timer:2000,
showConfirmButton:false
});
</script>
<?php
unset($_SESSION['msg'],$_SESSION['msgType']);
}
?>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.querySelector('input[name="photo"]');

    photoInput.addEventListener('change', function() {
        const allowedExt = ['jpg','jpeg','png'];
        const file = this.files[0];

        if(file){
            const ext = file.name.split('.').pop().toLowerCase();

            if(!allowedExt.includes(ext)){
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid File',
                    text: 'Only JPG, JPEG, PNG files are allowed!',
                    timer: 2000,
                    showConfirmButton: false
                });
                this.value = '';
            }
            else if(file.size > 5*1024*1024){
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Maximum allowed size is 5MB',
                    timer: 2000,
                    showConfirmButton: false
                });
                this.value = '';
            }
        }
    });
});
</script>

<script>
function showPhoto(src) {
    Swal.fire({
        imageUrl: src,
        imageAlt: 'User Photo',
        imageWidth: 300,
        imageHeight: 300,
        showCloseButton: true,
        showConfirmButton: false,
        background: '#f4f6fb',
    });
}
</script>


<script>
const deleteButtons = document.querySelectorAll('.delete-btn');

deleteButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const userId = this.dataset.id;

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php?delete=' + userId;
            }
        });
    });
});
</script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<!-- Buttons extension + dependencies -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
$(document).ready(function () {
    $('#userTable').DataTable({
        pageLength: 5,          
        lengthChange: false,    
        ordering: true,         
        searching: true,        
        info: true,             
        language: {
            search: "Search User:"
        },
        dom: 'Bfrtip',  // B = buttons, f = filter, r = processing, t = table, i = info, p = pagination
        buttons: [
                           // Copy to clipboard
                           // Export CSV
            'excel',       // Export Excel (XLSX)
            'pdf',         // Export PDF
                          // Print view
        ]
    });
});
</script>

</body>
</html>