<!DOCTYPE html>
<html lang="en">
<?php
require_once __DIR__ . '/layouts/header.php';
?>

<body class="account-page">

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="account-content">

            <div class="container">

                <!-- Account Logo -->
                <!-- Add your account logo code here -->
                <!-- /Account Logo -->

                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Register</h3>
                        <p class="account-subtitle">Access to our dashboard</p>

                        <!-- Account Form -->
                        <form action="" method="POST">
                            <?php
                            // Display alert message if any
                            alert_display();

                            // Generate CSRF token field
                            csrf_field();
                            ?>
                            <div class="form-group">
                                <label>First Name *</label>
                                <input class="form-control" type="text" name="first_name" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['first_name'])) ? $_POST['first_name'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name *</label>
                                <input class="form-control" type="text" name="last_name" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['last_name'])) ? $_POST['last_name'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input class="form-control" type="email" name="email" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['email'])) ? $_POST['email'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Password *</label>
                                <input class="form-control" type="password" name="password" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['password'])) ? $_POST['password'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password *</label>
                                <input class="form-control" type="password" name="repeat_password" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['repeat_password'])) ? $_POST['repeat_password'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Phone *</label>
                                <input class="form-control" type="text" name="phone" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['phone'])) ? $_POST['phone'] : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Role *</label>
                                <select class="form-control" name="role" required>
                                    <option value="admin" <?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    <option value="employee" <?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['role']) && $_POST['role'] == 'employee') ? 'selected' : ''; ?>>Employee</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Employee ID *</label>
                                <input class="form-control" type="text" name="employee_id" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['employee_id'])) ? $_POST['employee_id'] : ''; ?>" required>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Register</button>
                            </div>
                            <div class="account-footer">
                                <p>Already have an account? <a href="<?php echo base_url("/login")?>">Login</a></p>
                            </div>
                        </form>
                        <!-- /Account Form -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->

    <?php
    require_once __DIR__ . '/layouts/footer.php';
    ?>

</body>

</html>
