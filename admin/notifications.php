<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Security check
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
    exit;
}

// Fetch stats for summary cards
$stats = [
    'total' => 0,
    'sent' => 0,
    'failed' => 0,
    'today' => 0
];

// Mark all unread failed notifications as read since the admin is viewing the dashboard
try {
    $dbh->query("UPDATE tblnotifications SET is_read = 1 WHERE status = 'failed' AND is_read = 0");
} catch(Exception $e) {}

try {
    $statsQuery = $dbh->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today
        FROM tblnotifications
    ");
    $statsResult = $statsQuery->fetch(PDO::FETCH_ASSOC);
    if ($statsResult) {
        $stats = $statsResult;
    }
} catch (Exception $e) {}

// Handle Filters & Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';

$whereClauses = [];
$params = [];

if ($search !== '') {
    $whereClauses[] = "(recipient_email LIKE :search OR subject LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($statusFilter !== '') {
    $whereClauses[] = "status = :status";
    $params[':status'] = $statusFilter;
}

if ($typeFilter !== '') {
    // Overdue reminders have dynamic dates appended, so we use LIKE for overdue
    if ($typeFilter == 'Reminder_Overdue') {
        $whereClauses[] = "notification_type LIKE 'Reminder_Overdue%'";
    } else {
        $whereClauses[] = "notification_type = :type";
        $params[':type'] = $typeFilter;
    }
}

$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $whereClauses);
}

// Pagination logic
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Get total for pagination
$totalRecordsQuery = "SELECT COUNT(id) FROM tblnotifications $whereSql";
$stmt = $dbh->prepare($totalRecordsQuery);
foreach($params as $key => &$val) {
    $stmt->bindParam($key, $val);
}
$stmt->execute();
$totalRecords = $stmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch table data
$querySql = "SELECT * FROM tblnotifications $whereSql ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $dbh->prepare($querySql);
foreach($params as $key => &$val) {
    $stmt->bindParam($key, $val);
}
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_OBJ);

// Helper function to build pagination links cleanly
function buildQueryString($pageOverride) {
    $q = $_GET;
    $q['page'] = $pageOverride;
    return '?' . http_build_query($q);
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Online Library Management System | Notification Dashboard</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        .summary-box {
            padding: 20px;
            border-radius: 8px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
        }
        .summary-box h3 { margin: 0 0 5px 0; font-size: 36px; font-weight: bold; color: #0f172a; }
        .summary-box p { margin: 0; font-size: 14px; text-transform: uppercase; font-weight: 700; color: #64748b; }
        .bg-primary-dark { background: #f8fafc; border-bottom: 4px solid #3b82f6; }
        .bg-success-dark { background: #f0fdf4; border-bottom: 4px solid #22c55e; }
        .bg-danger-dark { background: #fef2f2; border-bottom: 4px solid #ef4444; }
        .bg-info-dark { background: #f0f9ff; border-bottom: 4px solid #0ea5e9; }
        
        .table-responsive { background: #fff; padding: 15px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .badge-sent { background-color: #166534; }
        .badge-failed { background-color: #dc2626; }
        
        .filter-panel { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        
        .modal-body pre { background: #1e293b; color: #f8f9fa; border: none; padding: 15px; overflow-x: auto; font-size: 12px; }
    </style>
</head>
<body>

<?php include('includes/header.php');?>

<div class="content-wrapper">
    <div class="container">
        <div class="row pad-botm">
            <div class="col-md-12">
                <h4 class="header-line">Notification Dashboard</h4>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="summary-box bg-primary-dark">
                    <h3><?php echo htmlentities($stats['total'] ?: 0); ?></h3>
                    <p>Total Notifications</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="summary-box bg-success-dark">
                    <h3><?php echo htmlentities($stats['sent'] ?: 0); ?></h3>
                    <p>Successfully Sent</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="summary-box bg-danger-dark">
                    <h3><?php echo htmlentities($stats['failed'] ?: 0); ?></h3>
                    <p>Failed Attempts</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="summary-box bg-info-dark">
                    <h3><?php echo htmlentities($stats['today'] ?: 0); ?></h3>
                    <p>Today's Notifications</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-md-12">
                <div class="filter-panel">
                    <form method="GET" action="notifications.php" class="form-inline">
                        <div class="form-group" style="margin-right: 15px;">
                            <label class="sr-only">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search Subject or Email..." value="<?php echo htmlentities($search); ?>">
                        </div>
                        <div class="form-group" style="margin-right: 15px;">
                            <label class="sr-only">Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="sent" <?php if($statusFilter == 'sent') echo 'selected'; ?>>Sent</option>
                                <option value="failed" <?php if($statusFilter == 'failed') echo 'selected'; ?>>Failed</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-right: 15px;">
                            <label class="sr-only">Type</label>
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="Issue" <?php if($typeFilter == 'Issue') echo 'selected'; ?>>Book Issued</option>
                                <option value="Return" <?php if($typeFilter == 'Return') echo 'selected'; ?>>Book Returned</option>
                                <option value="Reminder_3Day" <?php if($typeFilter == 'Reminder_3Day') echo 'selected'; ?>>3-Day Reminder</option>
                                <option value="Reminder_1Day" <?php if($typeFilter == 'Reminder_1Day') echo 'selected'; ?>>1-Day Reminder</option>
                                <option value="Reminder_Overdue" <?php if($typeFilter == 'Reminder_Overdue') echo 'selected'; ?>>Overdue Reminder</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Apply Filters</button>
                        <a href="notifications.php" class="btn btn-default">Reset</a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Subject</th>
                                <th>Recipient Email</th>
                                <th>Type</th>
                                <th>Date & Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($notifications) > 0) {
                                foreach($notifications as $notif) { 
                                    // Make ID safe for HTML attributes
                                    $htmlId = htmlentities($notif->id);
                            ?>
                                <tr>
                                    <td>
                                        <?php if($notif->status == 'sent') { ?>
                                            <span class="badge badge-sent"><i class="fa fa-check"></i> Sent</span>
                                        <?php } else { ?>
                                            <span class="badge badge-failed"><i class="fa fa-times"></i> Failed</span>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo htmlentities($notif->subject); ?></td>
                                    <td><?php echo htmlentities($notif->recipient_email); ?></td>
                                    <td><?php 
                                        $typeStr = htmlentities($notif->notification_type); 
                                        if(strpos($typeStr, 'Reminder_Overdue') === 0) {
                                            echo "Overdue Reminder";
                                        } else {
                                            echo str_replace('_', ' ', $typeStr);
                                        }
                                    ?></td>
                                    <td><?php echo htmlentities(date('M d, Y g:i A', strtotime($notif->created_at))); ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#notifModal_<?php echo $htmlId; ?>">
                                            <i class="fa fa-eye"></i> View
                                        </button>

                                        <!-- Modal for Details -->
                                        <div class="modal fade" id="notifModal_<?php echo $htmlId; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        <h4 class="modal-title">Notification Details #<?php echo $htmlId; ?></h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Status:</strong> 
                                                            <?php if($notif->status == 'sent') { ?>
                                                                <span class="text-success"><i class="fa fa-check"></i> Sent</span>
                                                            <?php } else { ?>
                                                                <span class="text-danger"><i class="fa fa-times"></i> Failed</span>
                                                            <?php } ?>
                                                        </p>
                                                        <p><strong>Recipient:</strong> <?php echo htmlentities($notif->recipient_email); ?></p>
                                                        <p><strong>Subject:</strong> <?php echo htmlentities($notif->subject); ?></p>
                                                        <p><strong>Type:</strong> <?php echo htmlentities($notif->notification_type); ?></p>
                                                        <p><strong>Date Sent:</strong> <?php echo htmlentities($notif->created_at); ?></p>
                                                        <p><strong>Related Issue ID:</strong> <?php echo htmlentities($notif->related_id ?: 'N/A'); ?></p>
                                                        
                                                        <?php if($notif->status == 'failed' && !empty($notif->error_message)) { ?>
                                                            <hr>
                                                            <h5><i class="fa fa-warning text-danger"></i> Technical Error Log</h5>
                                                            <pre><?php echo htmlentities($notif->error_message); ?></pre>
                                                            <small class="text-muted">Do not share technical SMTP logs with end users.</small>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Modal -->
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else { ?>
                                <tr>
                                    <td colspan="6" class="text-center" style="padding:30px;">
                                        <i class="fa fa-inbox fa-3x" style="color:#ccc;"></i>
                                        <h4 style="color:#888;">No notifications found matching your criteria.</h4>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    
                    <!-- Pagination Controls -->
                    <?php if ($totalPages > 1) { ?>
                        <div class="text-right">
                            <ul class="pagination" style="margin: 0;">
                                <?php if($page > 1) { ?>
                                    <li><a href="<?php echo buildQueryString($page-1); ?>">&laquo; Prev</a></li>
                                <?php } else { ?>
                                    <li class="disabled"><span>&laquo; Prev</span></li>
                                <?php } ?>
                                
                                <?php for($i = 1; $i <= $totalPages; $i++) { ?>
                                    <li class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a href="<?php echo buildQueryString($i); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php } ?>
                                
                                <?php if($page < $totalPages) { ?>
                                    <li><a href="<?php echo buildQueryString($page+1); ?>">Next &raquo;</a></li>
                                <?php } else { ?>
                                    <li class="disabled"><span>Next &raquo;</span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
                    
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
