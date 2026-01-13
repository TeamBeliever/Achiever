<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
<title>Users List</title>
<style>
table{border-collapse:collapse;width:100%;}
th,td{border:1px solid #ccc;padding:8px;text-align:center;}
</style>
</head>
<body>


<h2>Registered Users</h2>
<table>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Gender</th><th>Photo</th><th>Action</th>
</tr>


<?php
$result = mysqli_query($conn, "SELECT * FROM users");
while($row = mysqli_fetch_assoc($result)){
?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['full_name'] ?></td>
<td><?= $row['email'] ?></td>
<td><?= $row['mobile'] ?></td>
<td><?= $row['gender'] ?></td>
<td><img src="uploads/<?= $row['photo'] ?>" width="50"></td>
<td>
<a href="edit.php?id=<?= $row['id'] ?>">Edit</a> |
<a href="delete.php?id=<?= $row['id'] ?>">Delete</a>
</td>
</tr>
<?php } ?>
</table>


</body>
</html>