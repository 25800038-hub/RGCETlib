<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Fetch live library stats
$booksCount = 0;
$studentsCount = 0;
$teachersCount = 0;
$authorsCount = 0;

try {
    $q1 = $dbh->query("SELECT id FROM tblbooks");
    $booksCount = $q1 ? $q1->rowCount() : 0;

    $q2 = $dbh->query("SELECT id FROM tblstudents WHERE Status=1");
    $studentsCount = $q2 ? $q2->rowCount() : 0;

    $q3 = $dbh->query("SELECT id FROM tblteachers WHERE Status=1");
    $teachersCount = $q3 ? $q3->rowCount() : 0;

    $q4 = $dbh->query("SELECT id FROM tblauthors");
    $authorsCount = $q4 ? $q4->rowCount() : 0;
} catch (Exception $e) {
    // Graceful fallback
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description"
        content="Rajiv Gandhi College of Engineering and Technology - Central Library Management System" />
    <meta name="author" content="RGCET" />
    <title>RGCET Central Library | Digital Knowledge & Resource Hub</title>
    <!-- BOOTSTRAP CORE STYLE  -->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONT AWESOME STYLE  -->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLE  -->
    <link href="assets/css/style.css?v=1.5" rel="stylesheet" />
    <!-- GOOGLE FONTS -->
    <link
        href='https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap'
        rel='stylesheet' type='text/css' />

    <style>
        .landing-hero {
            background: linear-gradient(135deg, #231f19 0%, #3d382e 50%, #585043 100%);
            border-radius: 16px;
            padding: 40px 35px;
            color: #ffffff;
            margin-bottom: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.16);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .landing-hero::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(238, 235, 248, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .landing-hero h1 {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 32px;
            margin-top: 0;
            margin-bottom: 12px;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .landing-hero p {
            font-size: 16px;
            color: #cbd5e1;
            max-width: 650px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .hero-btn-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .hero-btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .hero-btn-primary {
            background: #9170E4;
            color: #ffffff !important;
            border: 1px solid #9170E4;
        }

        .hero-btn-primary:hover {
            background: #7b59cf;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(145, 112, 228, 0.4);
        }

        .hero-btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
        }

        .hero-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        /* Portal Cards */
        .portal-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 30px 25px;
            text-align: center;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.06);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .portal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        }

        .portal-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .icon-student {
            background: #eef2ff;
            color: #4f46e5;
        }

        .icon-teacher {
            background: #f0fdf4;
            color: #16a34a;
        }

        .icon-admin {
            background: #fef2f2;
            color: #dc2626;
        }

        .portal-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 20px;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .portal-desc {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        /* Stat Counter Cards */
        .stat-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 22px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-icon-wrap {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #9170E4;
            flex-shrink: 0;
        }

        .stat-number {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Service Cards */
        .service-box {
            background: #ffffff;
            border-radius: 10px;
            padding: 24px 20px;
            margin-bottom: 20px;
            border-left: 4px solid #9170E4;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .service-box h4 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 16px;
            color: #1e293b;
            margin-top: 0;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .service-box p {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 0;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <!------MENU SECTION START-->
    <?php include('includes/header.php'); ?>
    <!-- MENU SECTION END-->

    <div class="content-wrapper">
        <div class="container">

            <!-- Hero Section -->
            <div class="landing-hero">
                <div class="row">
                    <div class="col-md-7">
                        <span
                            style="display:inline-block; padding: 5px 14px; background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.18); border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 12px; color: #f5eedc;">
                            <i class="fa fa-university"></i> RGCET Central Library
                        </span>
                        <h1>Gateway to Knowledge & Academic Excellence</h1>
                        <p>Welcome to Rajiv Gandhi College of Engineering and Technology's Digital Library Portal.
                            Explore thousands of cataloged volumes, reserve books in real-time, access digital learning
                            resources, and manage borrowing records seamlessly.</p>
                        <div class="hero-btn-group">
                            <a href="studentlogin.php" class="hero-btn hero-btn-primary">
                                <i class="fa fa-graduation-cap"></i> Student Login
                            </a>
                            <a href="teacherlogin.php" class="hero-btn hero-btn-secondary">
                                <i class="fa fa-user-md"></i> Faculty Login
                            </a>
                            <a href="adminlogin.php" class="hero-btn hero-btn-secondary">
                                <i class="fa fa-shield"></i> Admin Portal
                            </a>
                        </div>
                    </div>
                    <div class="col-md-5 hidden-xs hidden-sm">
                        <!-- Carousel -->
                        <div id="landing-carousel" class="carousel slide" data-ride="carousel"
                            style="border-radius: 12px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.3);">
                            <div class="carousel-inner">
                                <div class="item active">
                                    <img src="assets/img/1.jpg" alt="RGCET Library"
                                        style="width: 100%; height: 240px; object-fit: cover;" />
                                </div>
                                <div class="item">
                                    <img src="assets/img/2.jpg" alt="Study Environment"
                                        style="width: 100%; height: 240px; object-fit: cover;" />
                                </div>
                                <div class="item">
                                    <img src="assets/img/3.jpg" alt="Digital Resources"
                                        style="width: 100%; height: 240px; object-fit: cover;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Overview -->
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon-wrap">
                            <i class="fa fa-book"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?php echo htmlentities($booksCount); ?>+</div>
                            <div class="stat-label">Cataloged Books</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon-wrap" style="color: #4f46e5;">
                            <i class="fa fa-users"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?php echo htmlentities($studentsCount); ?>+</div>
                            <div class="stat-label">Student Members</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon-wrap" style="color: #16a34a;">
                            <i class="fa fa-user-md"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?php echo htmlentities($teachersCount); ?>+</div>
                            <div class="stat-label">Faculty Members</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-icon-wrap" style="color: #ea580c;">
                            <i class="fa fa-pencil"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?php echo htmlentities($authorsCount); ?>+</div>
                            <div class="stat-label">Indexed Authors</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Choose Portal Section -->
            <div class="row pad-botm" style="margin-top: 15px;">
                <div class="col-md-12">
                    <center>
                        <h4 class="header-line">SELECT YOUR LOGIN PORTAL</h4>
                    </center>
                </div>
            </div>

            <div class="row" style="margin-bottom: 40px;">
                <!-- Student Portal -->
                <div class="col-md-4 col-sm-4" style="margin-bottom: 20px;">
                    <div class="portal-card">
                        <div>
                            <div class="portal-icon icon-student">
                                <i class="fa fa-graduation-cap"></i>
                            </div>
                            <h3 class="portal-title">Student Portal</h3>
                            <p class="portal-desc">Access catalog, search books, reserve titles for pickup, check active
                                loans, and monitor overdue alerts.</p>
                        </div>
                        <a href="studentlogin.php" class="btn btn-primary btn-block"
                            style="background: #4f46e5; border-color: #4f46e5; font-weight: 600; padding: 10px;">
                            <i class="fa fa-sign-in"></i> Student Login
                        </a>
                    </div>
                </div>

                <!-- Faculty Portal -->
                <div class="col-md-4 col-sm-4" style="margin-bottom: 20px;">
                    <div class="portal-card">
                        <div>
                            <div class="portal-icon icon-teacher">
                                <i class="fa fa-user-md"></i>
                            </div>
                            <h3 class="portal-title">Teacher Portal</h3>
                            <p class="portal-desc">Dedicated portal for professors & faculty to access advanced academic
                                journals, reserve books, and manage issues.</p>
                        </div>
                        <a href="teacherlogin.php" class="btn btn-success btn-block"
                            style="background: #16a34a; border-color: #16a34a; font-weight: 600; padding: 10px;">
                            <i class="fa fa-sign-in"></i> Teacher Login
                        </a>
                    </div>
                </div>

                <!-- Admin Portal -->
                <div class="col-md-4 col-sm-4" style="margin-bottom: 20px;">
                    <div class="portal-card">
                        <div>
                            <div class="portal-icon icon-admin">
                                <i class="fa fa-shield"></i>
                            </div>
                            <h3 class="portal-title">Admin Portal</h3>
                            <p class="portal-desc">Comprehensive administrative control center to manage books, authors,
                                issue/return cycles, and user registrations.</p>
                        </div>
                        <a href="adminlogin.php" class="btn btn-danger btn-block"
                            style="background: #dc2626; border-color: #dc2626; font-weight: 600; padding: 10px;">
                            <i class="fa fa-lock"></i> Admin Login
                        </a>
                    </div>
                </div>
            </div>

            <!-- Library Services & Timings -->
            <div class="row pad-botm">
                <div class="col-md-12">
                    <center>
                        <h4 class="header-line">LIBRARY SERVICES & TIMINGS</h4>
                    </center>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="service-box">
                        <h4><i class="fa fa-bookmark text-primary"></i> Online Reservations</h4>
                        <p>Browse books online and reserve them in advance. Pick them up conveniently at the circulation
                            desk within the pickup window.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-box">
                        <h4><i class="fa fa-clock-o text-success"></i> Working Hours</h4>
                        <p><strong>Monday – Saturday:</strong> 8:30 AM – 6:00 PM<br /><strong>Circulation Desk:</strong>
                            9:00 AM – 5:00 PM (Closed on Sundays & Holidays)</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="service-box">
                        <h4><i class="fa fa-info-circle text-info"></i> Borrowing Rules</h4>
                        <p>Students can borrow up to 3 books for 7 days. Faculty can borrow up to 5 books. Real-time
                            overdue alerts help prevent late fines.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- FOOTER SECTION START-->
    <?php include('includes/footer.php'); ?>
    <!-- FOOTER SECTION END-->

    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/custom.js"></script>

</body>

</html>