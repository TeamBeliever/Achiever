<?php
include 'db.php';

/* ID URL se lena */
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header("Location: view.php");
    exit;
}

/* Existing data fetch */
$query  = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);
$data   = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Record not found";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        *{
            box-sizing:border-box;
            font-family:"Segoe UI", Arial, sans-serif;
        }

        body{
            margin:0;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background: radial-gradient(circle at top, #6a11cb, #2575fc);
        }

        .card{
            width:100%;
            max-width:420px;
            background:rgba(255,255,255,0.15);
            backdrop-filter:blur(12px);
            border-radius:16px;
            padding:28px;
            box-shadow:0 20px 40px rgba(0,0,0,0.25);
        }

        .card h2{
            text-align:center;
            color:#fff;
            margin-bottom:22px;
            letter-spacing:1px;
        }

        .field{
            margin-bottom:15px;
        }

        .field label{
            display:block;
            margin-bottom:6px;
            color:#eaeaea;
            font-size:13px;
        }

        .field input,
        .field select{
            width:100%;
            padding:12px 14px;
            border:none;
            border-radius:10px;
            background:rgba(255,255,255,0.9);
            font-size:14px;
        }

        .field input:focus,
        .field select:focus{
            outline:none;
            box-shadow:0 0 0 2px #2575fc;
        }

        .field input[type="file"]{
            padding:9px;
            background:#fff;
        }

        .photo-preview{
            margin-top:8px;
        }

        .photo-preview img{
            width:70px;
            border-radius:8px;
            border:2px solid #fff;
        }

        button{
            width:100%;
            padding:13px;
            border:none;
            border-radius:12px;
            background:linear-gradient(135deg,#ff512f,#f09819);
            color:#fff;
            font-size:16px;
            font-weight:600;
            cursor:pointer;
            transition:transform 0.2s, box-shadow 0.2s;
        }

        button:hover{
            transform:translateY(-2px);
            box-shadow:0 8px 18px rgba(0,0,0,0.3);
        }

        @media(max-width:480px){
            .card{
                margin:15px;
                padding:22px;
            }
        }
    </style>
</head>

<body>

<div class="card">
    <h2>Edit Profile</h2>

    <form action="update.php" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
        <input type="hidden" name="old_photo" value="<?php echo $data['photo']; ?>">

        <div class="field">
            <label>Full Name</label>
            <input type="text" name="full_name"
                   value="<?php echo $data['full_name']; ?>" required>
        </div>

        <div class="field">
            <label>Email</label>
            <input type="email" name="email"
                   value="<?php echo $data['email']; ?>" required>
        </div>

        <div class="field">
            <label>Mobile Number</label>
            <input type="text" name="mobile"
                   value="<?php echo $data['mobile']; ?>"
                   pattern="[0-9]{10}"
                   maxlength="10"
                   title="Enter exactly 10 digit mobile number"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                   required>
        </div>

        <div class="field">
            <label>Gender</label>
            <select name="gender" required>
                <option value="">Select</option>
                <option value="Male" <?php if($data['gender']=="Male") echo "selected"; ?>>Male</option>
                <option value="Female" <?php if($data['gender']=="Female") echo "selected"; ?>>Female</option>
            </select>
        </div>

        <div class="field">
            <label>Current Photo</label>
            <div class="photo-preview">
                <img src="uploads/<?php echo $data['photo']; ?>">
            </div>
        </div>

        <div class="field">
            <label>Change Photo (optional)</label>
            <input type="file" name="photo">
        </div>

        <button type="submit">Update</button>

    </form>
</div>

</body>
</html>
