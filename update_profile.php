<?php
include 'config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['update_profile'])) {
        $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
        $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);

        mysqli_query($conn, "UPDATE `user_form` SET name = '$update_name', email = '$update_email' WHERE id = '$user_id'") or die('query failed');

        $old_pass = $_POST['old_pass'];
        $update_pass = mysqli_real_escape_string($conn, md5($_POST['update_pass']));
        $new_pass = mysqli_real_escape_string($conn, md5($_POST['new_pass']));
        $confirm_pass = mysqli_real_escape_string($conn, md5($_POST['confirm_pass']));

        if (!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)) {
            if ($update_pass != $old_pass) {
                $message[] = 'old password not matched!';
            } elseif ($new_pass != $confirm_pass) {
                $message[] = 'confirm password not matched!';
            } else {
                mysqli_query($conn, "UPDATE `user_form` SET password = '$confirm_pass' WHERE id = '$user_id'") or die('query failed');
                $message[] = 'password updated successfully!';
            }
        }

        $update_image = $_FILES['update_image']['name'];
        $update_image_size = $_FILES['update_image']['size'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_folder = 'admin/uploaded_img/' . $update_image;

        if (!empty($update_image)) {
            if ($update_image_size > 2000000) {
                $message[] = 'image is too large';
            } else {
                $image_update_query = mysqli_query($conn, "UPDATE `user_form` SET image = '$update_image' WHERE id = '$user_id'") or die('query failed');
                if ($image_update_query) {
                    move_uploaded_file($update_image_tmp_name, $update_image_folder);
                }
                $message[] = 'image updated successfully!';
            }
        }
    }
} else {
    // Redirect the user to the login page or handle the case when the user is not logged in
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Profile</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/update_profile.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'header.php'; ?>
  
<div class="update-profile">

   <?php
      $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select) > 0){
         $fetch = mysqli_fetch_assoc($select);
      }
   ?>

   <form action="" method="post" enctype="multipart/form-data">
      <?php
         if($fetch['image'] == ''){
            echo '<img src="images/default-avatar.png" alt="Default Avatar">';
         } else {
            echo '<img src="admin/uploaded_img/'.$fetch['image'].'" alt="Profile Image">';
         }
         if(isset($message)){
            foreach($message as $msg){
               echo '<div class="message">'.$msg.'</div>';
            }
         }
      ?>
      <div class="flex">
         <div class="inputBox">
            <span><b>Username:</b></span>
            <input type="text" name="update_name" value="<?php echo $fetch['name']; ?>" class="box">
            <span><b>Your Email:</b></span>
            <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">
            <span><b>Update Your Picture:</b></span>
            <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">
         </div>
         <div class="inputBox">
            <input type="hidden" name="old_pass" value="<?php echo $fetch['password']; ?>">
            <span><b>Old Password:</b></span>
            <input type="password" name="update_pass" placeholder="Enter Previous Password" class="box">
            <span><b>New Password:</b></span>
            <input type="password" name="new_pass" placeholder="Enter New Password" class="box">
            <span><b>Confirm Password:</b></span>
            <input type="password" name="confirm_pass" placeholder="Confirm New Password" class="box">
         </div>
      </div>
      <input type="submit" value="Update Profile" name="update_profile" class="btn">
      <br>
      <a href="login.php" class="delete-btn">Log Out</a>
   </form>

</div>

</body>
<?php include 'footer.php'; ?>

</html>
