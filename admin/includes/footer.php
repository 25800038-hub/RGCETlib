<section class="site-footer">
    <!-- Banner Image Strip -->


    <!-- Main Footer Links -->
    <div class="footer-links-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <h5 class="footer-col-title">Admin Panel</h5>
                    <ul class="footer-link-list">
                        <li><a href="dashboard.php"><i class="fa fa-th-large"></i> Dashboard</a></li>
                        <li><a href="manage-books.php"><i class="fa fa-book"></i> Manage Books</a></li>
                        <li><a href="manage-reservations.php"><i class="fa fa-bookmark"></i> Reservations</a></li>
                        <li><a href="manage-issued-books.php"><i class="fa fa-exchange"></i> Issued Books</a></li>
                        <li><a href="overdue-details.php"><i class="fa fa-clock-o"></i> Overdue Books</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-6">
                    <h5 class="footer-col-title">Catalog</h5>
                    <ul class="footer-link-list">
                        <li><a href="add-book.php"><i class="fa fa-plus-circle"></i> Add New Book</a></li>
                        <li><a href="manage-books.php"><i class="fa fa-list"></i> Manage Books</a></li>
                        <li><a href="add-author.php"><i class="fa fa-pencil"></i> Add Author</a></li>
                        <li><a href="manage-authors.php"><i class="fa fa-users"></i> Manage Authors</a></li>
                        <li><a href="manage-categories.php"><i class="fa fa-folder-open"></i> Categories</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-6">
                    <h5 class="footer-col-title">Students</h5>
                    <ul class="footer-link-list">
                        <li><a href="reg-students.php"><i class="fa fa-users"></i> Registered Students</a></li>
                        <li><a href="add-student.php"><i class="fa fa-plus-circle"></i> Add Student</a></li>
                        <li><a href="issue-book.php"><i class="fa fa-plus-circle"></i> Issue New Book</a></li>
                        <li><a href="manage-issued-books.php"><i class="fa fa-list-alt"></i> Issued History</a></li>
                        <li><a href="manage-reservations.php"><i class="fa fa-calendar-o"></i> Pickup Requests</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-6">
                    <h5 class="footer-col-title">Admin Account</h5>
                    <ul class="footer-link-list">
                        <li><a href="change-password.php"><i class="fa fa-lock"></i> Change Password</a></li>
                        <li><a href="#"><i class="fa fa-question-circle"></i> Library Timings</a></li>
                        <li><a href="#"><i class="fa fa-phone"></i> Contact Support</a></li>
                        <li><a href="../index.php"><i class="fa fa-home"></i> Student Portal</a></li>
                        <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom Bar -->
    <div class="footer-bottom-bar">
        <div class="container">
            <div class="footer-bottom-inner">
                <div class="footer-bottom-left">
                    <img src="assets/img/logor.png" alt="RGCET Logo" class="footer-bottom-logo" />
                    <div class="footer-bottom-text">
                        <strong>Rajiv Gandhi College of Engineering and Technology</strong><br />
                        <small>Puducherry &bull; Administrative Control Center &bull; Library Management</small>
                    </div>
                </div>
                <div class="footer-bottom-right">
                    &copy; <?php echo date('Y'); ?> RGCET Online Library Management System.<br />
                    <small>All Rights Reserved.</small>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* ============================================================
   SITE FOOTER — RGCET gray Theme (Admin)
   ============================================================ */
    .site-footer {
        margin-top: 50px;
        font-family: 'Inter', 'Open Sans', sans-serif;
    }

    .footer-banner {
        width: 100%;
        overflow: hidden;
        border-top: 4px solid #9170E4;
        line-height: 0;
    }

    .footer-banner img {
        width: 100%;
        height: auto;
        max-height: 220px;
        object-fit: cover;
        object-position: left center;
        display: block;
    }

    .footer-links-section {
        background-color: rgba(28, 26, 31, 1);
        padding: 40px 0 30px;
    }

    .footer-col-title {
        font-family: 'Outfit', 'Inter', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: #ffffffff;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin: 0 0 18px 0;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(165, 180, 252, 0.2);
    }

    .footer-link-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-link-list li {
        margin-bottom: 9px;
        line-height: 1.4;
    }

    .footer-link-list li a {
        color: rgba(151, 153, 154, 1);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: color 0.18s ease, padding-left 0.18s ease;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .footer-link-list li a:hover {
        color: #ffffff;
        padding-left: 4px;
    }

    .footer-link-list li a .fa {
        font-size: 11px;
        width: 14px;
        text-align: center;
        color: #ffffffff;
        flex-shrink: 0;
    }

    .footer-bottom-bar {
        background-color: rgba(28, 26, 31, 1);
        padding: 18px 0;
        border-top: 1px solid rgba(145, 112, 228, 0.25);
    }

    .footer-bottom-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }

    .footer-bottom-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .footer-bottom-logo {
        height: 44px;
        width: auto;
        object-fit: contain;

        opacity: 0.85;
    }

    .footer-bottom-text {
        color: #ffffffff;
        font-size: 13px;
        line-height: 1.5;
    }

    .footer-bottom-text strong {
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
    }

    .footer-bottom-text small {
        color: #ffffffff;
        font-size: 11px;
    }

    .footer-bottom-right {
        color: #ffffffff;
        font-size: 12px;
        text-align: right;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .footer-bottom-inner {
            flex-direction: column;
            text-align: center;
        }

        .footer-bottom-left {
            justify-content: center;
        }

        .footer-bottom-right {
            text-align: center;
        }

        .footer-links-section .col-md-3 {
            margin-bottom: 25px;
        }
    }
</style>