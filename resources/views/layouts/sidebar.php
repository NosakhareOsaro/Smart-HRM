<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Main</span>
                </li>
                <li class="submenu">
                    <a href="#"><i class="la la-dashboard"></i> <span>Dashboard</span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <?php if ($activeUserData['role'] === 'admin'): ?>
                            <!-- Display Admin Dashboard link if user role is admin -->
                            <li><a class="<?= ($activePage == 'admin_dashboard') ? 'active' : ''; ?>" href="<?php echo base_url('/admin-dashboard') ?>">Admin Dashboard</a></li>
                        <?php else: ?>
                            <!-- Display Employee Dashboard link for other roles -->
                            <li><a class="<?= ($activePage == 'employee_dashboard') ? 'active' : ''; ?>" href="<?php echo base_url('/employee-dashboard') ?>">Employee Dashboard</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                
                <?php if ($activeUserData['role'] === 'admin'): ?>
                <li class="menu-title">
                    <span>Employees</span>
                </li>
                
                <li class="submenu">
                    <a href="#" class="noti-dot"><i class="la la-user"></i> <span>Employees</span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="<?= ($activePage == 'employees') ? 'active' : ''; ?>" href="<?php echo base_url('/employees') ?>">All Employees</a></li>
                        <li><a class="<?= ($activePage == 'attendance') ? 'active' : ''; ?>" href="<?php echo base_url('/attendance') ?>">Attendance</a></li>
                    </ul>
                </li>
                
                <li class="menu-title">
                    <span>HR</span>
                </li>
                
                <li class="submenu">
                    <a href="#"><i class="la la-money"></i> <span>Payroll</span> <span class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="<?= ($activePage == 'salary') ? 'active' : ''; ?>" href="<?php echo base_url('/salary') ?>">Employee Salary</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
