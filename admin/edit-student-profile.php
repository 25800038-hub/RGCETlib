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
    $stdid = $_GET['stdid'];
    
    if(isset($_POST['update']))
    {
        $fname = $_POST['fullanme'];
        $mobileno = $_POST['mobileno'];
        $email = trim($_POST['email']);
        $department = $_POST['department'];
        $year = $_POST['year'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Please enter a valid email address.');</script>";
        }
        else {
            if(!empty($_POST['password'])) {
                $password = md5($_POST['password']);
                $sql = "UPDATE tblstudents SET FullName=:fname, MobileNumber=:mobileno, EmailId=:email, Department=:department, Year=:year, Password=:password WHERE StudentId=:stdid";
                $query = $dbh->prepare($sql);
                $query->bindParam(':password', $password, PDO::PARAM_STR);
            } else {
                $sql = "UPDATE tblstudents SET FullName=:fname, MobileNumber=:mobileno, EmailId=:email, Department=:department, Year=:year WHERE StudentId=:stdid";
                $query = $dbh->prepare($sql);
            }
            
            $query->bindParam(':fname', $fname, PDO::PARAM_STR);
            $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':department', $department, PDO::PARAM_STR);
            $query->bindParam(':year', $year, PDO::PARAM_STR);
            $query->bindParam(':stdid', $stdid, PDO::PARAM_STR);
            $query->execute();

            $_SESSION['msg']="Student profile updated successfully";
            header('location:reg-students.php');
            exit;
        }
    }
    
    // Fetch current details
    $sql = "SELECT * FROM tblstudents WHERE StudentId=:stdid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':stdid', $stdid, PDO::PARAM_STR);
    $query->execute();
    $student = $query->fetch(PDO::FETCH_OBJ);
    
    if(!$student) {
        header('location:reg-students.php');
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
    <title>Online Library Management System | Edit Student</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <script type="text/javascript">
    var originalEmail = "<?php echo htmlentities($student->EmailId); ?>";
    
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
                <h4 class="header-line">Edit Student Profile (<?php echo htmlentities($student->StudentId); ?>)</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9 col-md-offset-1">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Student Profile Details
                    </div>
                    <div class="panel-body">
                        <form name="signup" id="profileForm" method="post">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input class="form-control" type="text" name="fullanme" value="<?php echo htmlentities($student->FullName); ?>" autocomplete="off" required />
                            </div>

                            <div class="form-group">
                                <label>Mobile Number :</label>
                                <input class="form-control" type="text" name="mobileno" value="<?php echo htmlentities($student->MobileNumber); ?>" maxlength="10" autocomplete="off" required />
                            </div>

                            <div class="form-group">
                                <label>Department :</label>
                                <select class="form-control" name="department" id="department" required onchange="updateYearOptions()">
                                    <option value="">Select Department</option>
                                    <?php
                                    $depts = ["MCA", "MBA", "AI&ML", "AI&DS", "CSE", "ECE", "IT"];
                                    foreach($depts as $d) {
                                        $selected = ($student->Department == $d) ? 'selected' : '';
                                        echo '<option value="'.$d.'" '.$selected.'>'.$d.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Year :</label>
                                <select class="form-control" name="year" id="year" required>
                                    <option value="<?php echo htmlentities($student->Year); ?>"><?php echo htmlentities($student->Year); ?></option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" type="email" name="email" id="emailid" value="<?php echo htmlentities($student->EmailId); ?>" autocomplete="off" required />
                            </div>

                            <div class="form-group">
                                <label>Current Password</label>
                                <input class="form-control" type="text" value="<?php echo htmlentities($student->Password); ?>" readonly />
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
<script>
function updateYearOptions() {
    var dept = document.getElementById("department").value;
    var yearSelect = document.getElementById("year");
    
    var currentYear = "<?php echo htmlentities($student->Year); ?>";
    yearSelect.innerHTML = '<option value="">Select Year</option>';
    
    if (dept === "") return;
    
    var years = [];
    if (dept === "MCA" || dept === "MBA") {
        years = ["I", "II"];
    } else {
        years = ["I", "II", "III", "IV", "V"];
    }
    
    for (var i = 0; i < years.length; i++) {
        var opt = document.createElement("option");
        opt.value = years[i];
        opt.innerHTML = years[i];
        if(years[i] == currentYear) {
            opt.selected = true;
        }
        yearSelect.appendChild(opt);
    }
}
// Initialize year options on load
$(document).ready(function() {
    updateYearOptions();
});
</script>
</body>
</html>
<?php } ?>
