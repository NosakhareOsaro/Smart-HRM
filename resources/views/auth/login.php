<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/layouts/header.php'; ?>

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
                        <h3 class="account-title">Login</h3>
                        <!-- Account Form -->
                        <form action="" method="POST">
                            <?php alert_display(); ?>
                            <?php csrf_field(); ?>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input class="form-control" type="text" name="email" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['email'])) ? $_POST['email'] : '';?>" required>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <label>Password</label>
                                    </div>
                                    <div class="col-auto">
                                        <a class="text-muted" href="">
                                            Forgot password?
                                        </a>
                                    </div>
                                </div>
                                <input class="form-control" type="password" name="password" value="<?php echo (isset($has_form_error) && $has_form_error == true && isset($_POST['password'])) ? $_POST['password'] : '';?>" required>
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Login</button>
                            </div>
                        </form>
                        <!-- /Account Form -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->

    <?php require_once __DIR__ . '/layouts/footer.php'; ?>
</body>
</html>
