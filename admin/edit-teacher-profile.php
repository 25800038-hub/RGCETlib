<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])==0)
{
    header('location:index.php');
}
else
{
    $teaid = $_GET['teaid'];
    
    if(isset($_POST['update']))
    {
        $fname = $_POST['fullanme'];
        $mobileno = $_POST['mobileno'];
        $email = trim($_POST['email']);
        $department = $_POST['department'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Please enter a valid email address.');</script>";
        }
        else {
            if(!empty($_POST['password'])) {
                $password = md5($_POST['password']);
                $sql = "UPDATE tblteachers SET FullName=:fname, MobileNumber=:mobileno, EmailId=:email, Department=:department, Password=:password WHERE TeacherId=:teaid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':password', $password, PDO::PARAM_STR);
            } else {
                $sql = "UPDATE tblteachers SET FullName=:fname, MobileNumber=:mobileno, EmailId=:email, Department=:department WHERE TeacherId=:teaid";
                $query = $dbh->prepare($sql);
            }
            
            $query->bindParam(':fname', $fname, PDO::PARAM_STR);
            $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':department', $department, PDO::PARAM_STR);
            $query->bindParam(':teaid', $teaid, PDO::PARAM_STR);
            $query->execute();

            $_SESSION['msg']="Teacher profile updated successfully";
            header('location:reg-teachers.php');
            exit;
        }
    }
    
    // Fetch current details
    $sql = "SELECT * FROM tblteachers WHERE TeacherId=:teaid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':teaid', $teaid, PDO::PARAM_STR);
    $query->execute();
    $teacher = $query->fetch(PDO::FETCH_OBJ);
    
    if(!$teacher) {
        header('location:reg-teachers.php');
        exit;
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Online Library Management System | Edit Teacher</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <script type="text/javascript">
    var originalEmail = "<?php echo htmlentities($teacher->EmailId); ?>";
    
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            var email = document.signup.email.value.trim();
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address.");
                document.signup.email.focus();
                e.preventDefault();
                return false;
            }
            
            var pw = document.signup.password.value;
            var cpw = document.signup.confirmpassword.value;

            if(pw != "" && pw != cpw)
            {
                alert("Password and Confirm Password Field do not match !!");
                document.signup.confirmpassword.focus();
                e.preventDefault();
                return false;
            }

            if(email !== originalEmail) {
                e.preventDefault(); // Stop normal submission to check email
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "check_email_exist.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var performSubmit = false;
                        if(xhr.responseText.trim() === 'true') {
                            if(confirm("Email already Exist Are you sure to update")) {
                                performSubmit = true;
                            }
                        } else {
                            performSubmit = true;
                        }
                        
                        if(performSubmit) {
                            var hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'update';
                            hiddenInput.value = '1';
                            document.getElementById('profileForm').appendChild(hiddenInput);
                            document.getElementById('profileForm').submit();
                        }
                    }
                };
                xhr.send("emailid=" + encodeURIComponent(email));
            }
        });
    });
    </script>
</head>
<body>
<?php include('includes/header.php');?>
<div class="content-wrapper">
    <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Edit Teacher Profile (<?php echo htmlentities($teacher->TeacherId); ?>)</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9 col-md-offset-1">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Teacher Profile Details
                    </div>
                    <div class="panel-body">
                        <form name="signup" id="profileForm" method="post">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input class="form-control" type="text" name="fullanme" value="<?php echo htmlentities($teacher->FullName); ?>" autocomplete="off" required />
                            </div>

                            <div class="form-group">
                                <label>Mobile Number :</label>
                                <input class="form-control" type="text" name="mobileno" value="<?php echo htmlentities($teacher->MobileNumber); ?>" maxlength="10" autocomplete="off" required />
                            </div>

                            <div class="form-group">
                                <label>Department :</label>
                                <select class="form-control" name="department" id="department" required>
                                    <option value="">Select Department</option>
                                    <?php
                                    $depts = ["MCA", "MBA", "AI&ML", "AI&DS", "CSE", "ECE", "IT"];
                                    foreach($depts as $d) {
                                        $selected = ($teacher->Department == $d) ? 'selected' : '';
                                        echo '<option value="'.$d.'" '.$selected.'>'.$d.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" type="email" name="email" id="emailid" value="<?php echo htmlentities($teacher->EmailId); ?>" autocomplete="off" required />
                            </div>

                            <div class="form-group">
                                <label>Current Password</label>
                                <input class="form-control" type="text" value="<?php echo htmlentities($teacher->Password); ?>" readonly />
                            </div>

                            <div class="alert alert-warning">
                                <strong>Note:</strong> Leave the password fields blank if you do not want to change the password.
                            </div>

                            <div class="form-group">
                                <label>New Password</label>
                                <input class="form-control" type="password" name="password" autocomplete="off" />
                            </div>

                            <div class="form-group">
                                <label>Confirm New Password </label>
                                <input class="form-control" type="password" name="confirmpassword" autocomplete="off" />
                            </div>

                            <button type="submit" name="update" class="btn btn-info" id="btnSubmit">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('includes/footer.php');?>
<script src="assets/js/jquery-1.10.2.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/custom.js"></script>
</body>
</html>
<?php } ?>
