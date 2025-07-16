<?php
// Get current page name
$current_page = basename($_SERVER['PHP_SELF']);

// Function to check if a menu item is active
function isActive($page) {
    global $current_page;
    return $current_page === $page ? 'active' : '';
}

// Function to check if a menu group is active
function isGroupActive($pages) {
    global $current_page;
    return in_array($current_page, $pages) ? 'active' : '';
}
?>
<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="index.php" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
<img src="../assets/images/logo-dark.svg" class="img-fluid logo-lg" alt="logo">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <!-- Dashboard -->
                <li class="pc-item <?= isActive('dashboard.php') ?>">
                    <a href="index.php" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                <!-- Organization Management -->
                <li class="pc-item pc-caption">
                    <label>Organization Management</label>
                    <i class="ti ti-building"></i>
                </li>
                <li class="pc-item <?= isActive('org_add.php') ?>">
                    <a href="org_add" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-circle-plus"></i></span>
                        <span class="pc-mtext">Add Organization</span>
                    </a>
                </li>
                <li class="pc-item <?= isActive('org_update.php') ?>">
                    <a href="org_update" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-replace"></i></span>
                        <span class="pc-mtext">Update Organization</span>
                    </a>
                </li>
                <li class="pc-item <?= isActive('org_deactivate.php') ?>">
                    <a href="org_deactivate" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-circle-minus"></i></span>
                        <span class="pc-mtext">Deactivate Organization</span>
                    </a>
                </li>

                <!-- User Management -->
                <li class="pc-item pc-caption">
                    <label>User Management</label>
                    <i class="ti ti-users"></i>
                </li>
                <li class="pc-item <?= isActive('user_update.php') ?>">
                    <a href="user_update" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-replace"></i></span>
                        <span class="pc-mtext">Update User</span>
                    </a>
                </li>
                <li class="pc-item <?= isActive('user_deactivate.php') ?>">
                    <a href="user_deactivate" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-user-off"></i></span>
                        <span class="pc-mtext">Deactivate User</span>
                    </a>
                </li>

                <!-- Business Verifications -->
                <li class="pc-item pc-caption">
                    <label>Business Verifications</label>
                    <i class="ti ti-building"></i>
                </li>
                <li class="pc-item <?= isActive('business_verifications.php') ?>">
                    <a href="business_verifications" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-shield-check"></i></span>
                        <span class="pc-mtext">Business Verifications</span>
                    </a>
                </li>

                <!-- Settings -->
                <li class="pc-item pc-caption">
                    <label>Settings</label>
                    <i class="ti ti-settings"></i>
                </li>
                <li class="pc-item">
                    <a href="../auth/logout" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-power"></i></span>
                        <span class="pc-mtext">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end --> 