<!-- TOP BRANDING & INSTITUTIONAL HEADER (ADMIN) -->
<div class="top-branding-bar">
    <div class="container">
        <div class="branding-wrapper">
            <div class="branding-left">
                <a href="dashboard.php" class="logo-link">
                    <img src="assets/img/logorr.png" alt="RGCET Crest" class="college-logo" />
                </a>
                <div class="college-details">
                    <h2 class="college-name">RAJIV GANDHI COLLEGE OF ENGINEERING & TECHNOLOGY</h2>
                    <div class="college-sub-bar">
                        <span class="lib-badge" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);"><i class="fa fa-shield"></i> Administration Portal</span>
                        <span class="lib-subtext">Central Library System Control Hub</span>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['alogin']) && $_SESSION['alogin']) { 
                $adminName = isset($_SESSION['FullName']) && $_SESSION['FullName'] != '' ? $_SESSION['FullName'] : $_SESSION['alogin'];
            ?>
                <div class="branding-user-panel">
                    <div class="user-chip">
                        <div class="user-avatar" style="background: #dc2626;">
                            <i class="fa fa-shield"></i>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlentities($adminName); ?></span>
                            <span class="user-role" style="color: #dc2626;">Administrator</span>
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

                        <li>
                            <a href="#" class="dropdown-toggle" id="ddlCategories" data-toggle="dropdown"><i class="fa fa-folder-open"></i> Categories <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddlCategories">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="add-category.php"><i class="fa fa-plus-circle"></i> Add Category</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="manage-categories.php"><i class="fa fa-list"></i> Manage Categories</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="dropdown-toggle" id="ddlAuthors" data-toggle="dropdown"><i class="fa fa-pencil"></i> Authors <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddlAuthors">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="add-author.php"><i class="fa fa-plus-circle"></i> Add Author</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="manage-authors.php"><i class="fa fa-users"></i> Manage Authors</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="dropdown-toggle" id="ddlBooks" data-toggle="dropdown"><i class="fa fa-book"></i> Books <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddlBooks">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="add-book.php"><i class="fa fa-plus-circle"></i> Add Book</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="manage-books.php"><i class="fa fa-list-alt"></i> Manage Books</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="#" class="dropdown-toggle" id="ddlIssueBooks" data-toggle="dropdown"><i class="fa fa-exchange"></i> Issue Books <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddlIssueBooks">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="issue-book.php"><i class="fa fa-plus"></i> Issue New (Student)</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="manage-issued-books.php"><i class="fa fa-check-square-o"></i> Manage Issued (Student)</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="issue-book-teacher.php"><i class="fa fa-plus"></i> Issue New (Teacher)</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="manage-issued-books-teacher.php"><i class="fa fa-check-square-o"></i> Manage Issued (Teacher)</a></li>
                            </ul>
                        </li>
                        <li><a href="manage-reservations.php"><i class="fa fa-bookmark"></i> Reservations</a></li>
                        <li>
                            <a href="#" class="dropdown-toggle" id="ddlStudents" data-toggle="dropdown"><i class="fa fa-graduation-cap"></i> Students <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddlStudents">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="add-student.php"><i class="fa fa-user-plus"></i> Add Student</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="reg-students.php"><i class="fa fa-users"></i> Reg Students</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="dropdown-toggle" id="ddlTeachers" data-toggle="dropdown"><i class="fa fa-user-md"></i> Teachers <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddlTeachers">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="add-teacher.php"><i class="fa fa-user-plus"></i> Add Teacher</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="reg-teachers.php"><i class="fa fa-users"></i> Reg Teachers</a></li>
                            </ul>
                        </li>
                        <li><a href="change-password.php"><i class="fa fa-key"></i> Password</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>