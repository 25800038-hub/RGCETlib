<!-- TOP BRANDING & INSTITUTIONAL HEADER -->
<div class="top-branding-bar">
    <div class="container">
        <div class="branding-wrapper">
            <div class="branding-left">
                <a href="index.php" class="logo-link">
                    <img src="assets/img/logorr.png" alt="RGCET College Crest" class="college-logo" />
                </a>
                <div class="college-details">
                    <h2 class="college-name">RAJIV GANDHI COLLEGE OF ENGINEERING & TECHNOLOGY</h2>
                    <div class="college-sub-bar">
                        <span class="lib-badge"><i class="fa fa-university"></i> Central Library</span>
                        <span class="lib-subtext">Digital Knowledge &amp; Resource Hub &bull; Puducherry</span>
                    </div>
                </div>
            </div>

            <?php if ((isset($_SESSION['login']) && $_SESSION['login']) || (isset($_SESSION['tlogin']) && $_SESSION['tlogin'])) { 
                $userName = isset($_SESSION['FullName']) && $_SESSION['FullName'] != '' ? $_SESSION['FullName'] : (isset($_SESSION['login']) ? $_SESSION['login'] : $_SESSION['tlogin']);
                $isTeacher = isset($_SESSION['tlogin']) && $_SESSION['tlogin'];
                $roleLabel = $isTeacher ? 'Faculty Member' : 'Student Member';
                $roleIcon = $isTeacher ? 'fa-user-md' : 'fa-graduation-cap';
            ?>
                <div class="branding-user-panel">
                    <div class="user-chip">
                        <div class="user-avatar">
                            <i class="fa <?php echo $roleIcon; ?>"></i>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlentities($userName); ?></span>
                            <span class="user-role"><?php echo htmlentities($roleLabel); ?></span>
                        </div>
                    </div>
                    <a href="logout.php" class="btn-logout" title="Sign Out">
                        <i class="fa fa-sign-out"></i> <span>LOGOUT</span>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<!-- LOGO HEADER END-->

<!-- NAVIGATION MENU SECTION -->
<?php if (isset($_SESSION['login']) && $_SESSION['login']) { ?>
    <section class="menu-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <div class="navbar-collapse collapse">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a href="dashboard.php"><i class="fa fa-th-large"></i> Dashboard</a></li>
                            <li><a href="listed-books.php"><i class="fa fa-book"></i> Books Catalog</a></li>
                            <li><a href="my-reservations.php"><i class="fa fa-bookmark"></i> My Reservations</a></li>
                            <li><a href="issued-books.php"><i class="fa fa-check-square-o"></i> Issued Books</a></li>
                            <li><a href="overdue-details.php"><i class="fa fa-clock-o"></i> Overdue Books</a></li>
                            <li>
                                <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown">
                                    <i class="fa fa-user-circle"></i> My Account <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="my-profile.php"><i class="fa fa-id-card"></i> My Profile</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="change-password.php"><i class="fa fa-key"></i> Change Password</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } elseif (isset($_SESSION['tlogin']) && $_SESSION['tlogin']) { ?>
    <section class="menu-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <div class="navbar-collapse collapse">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a href="teacher-dashboard.php"><i class="fa fa-th-large"></i> Dashboard</a></li>
                            <li><a href="listed-books.php"><i class="fa fa-book"></i> Books Catalog</a></li>
                            <li><a href="issued-books.php"><i class="fa fa-check-square-o"></i> Issued Books</a></li>
                            <li>
                                <a href="#" class="dropdown-toggle" id="ddlmenuItem" data-toggle="dropdown">
                                    <i class="fa fa-user-circle"></i> My Account <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="ddlmenuItem">
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="teacher-profile.php"><i class="fa fa-id-card"></i> My Profile</a></li>
                                    <li role="presentation"><a role="menuitem" tabindex="-1" href="teacher-change-password.php"><i class="fa fa-key"></i> Change Password</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } else { ?>
    <section class="menu-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <div class="navbar-collapse collapse">
                        <ul id="menu-top" class="nav navbar-nav navbar-right">
                            <li><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
                            <li><a href="studentlogin.php"><i class="fa fa-graduation-cap"></i> Student Login</a></li>
                            <li><a href="teacherlogin.php"><i class="fa fa-user-md"></i> Teacher Login</a></li>
                            <li><a href="adminlogin.php"><i class="fa fa-shield"></i> Admin Login</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>