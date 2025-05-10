<?php
if (session()->get('logged_in')) {
    header('Location: /admin');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Simple DTR Manager</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="<?= base_url("assets/vendors/mdi/css/materialdesignicons.min.css") ?>">
    <link rel="stylesheet" href="<?= base_url("assets/vendors/css/vendor.bundle.base.css") ?>">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="<?= base_url("assets/css/horizontal-layout/style.css") ?>">
    <!-- endinject -->
    <link rel="shortcut icon" href="<?= base_url("assets/images/favicon.png") ?>" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="main-panel">
                <div class="content-wrapper d-flex align-items-center auth px-0">
                    <div class="row w-100 mx-0">
                        <div class="col-lg-4 mx-auto">
                            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                                <div class="brand-logo text-center">
                                    <img src="<?= base_url("assets/images/MSSD_simplified.png") ?>" alt="logo">
                                </div>
                                <h4 class="text-center">Login to continue</h4>
                                <form id="login-form" class="pt-3">
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-lg" id="password" placeholder="Password" required>
                                        <div class="invalid-feedback">
                                            Please provide a password.
                                        </div>
                                    </div>
                                    <div class="alert d-none text-center" id="notification" role="alert">
                                    </div>
                                </form>
                                <div class="mt-3 d-grid gap-2">
                                    <button type="button" class="btn btn-primary btn-lg fw-medium auth-form-btn" id="login-btn">SIGN IN</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="<?= base_url('assets/vendors/js/vendor.bundle.base.js') ?>"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="<?= base_url('assets/js/off-canvas.js') ?>"></script>
    <script src="<?= base_url('assets/js/hoverable-collapse.js') ?>"></script>
    <script src="<?= base_url('assets/js/template.js') ?>"></script>
    <script src="<?= base_url('assets/js/settings.js') ?>"></script>
    <script src="<?= base_url('assets/js/todolist.js') ?>"></script>
    <!-- endinject -->
    <script>
        $(function() {
            $('#login-btn').click(function(e) {
                $('#notification').addClass('d-none');
                var $form = $('#login-form');
                if ($form[0].checkValidity() === false) {
                    e.preventDefault();
                    e.stopPropagation();
                    $form.addClass('was-validated');
                } else {
                    let pass = $('#password').val();
                    $.post('auth', {
                        pass: pass
                    }, function(res) {
                        if (res.status == 'success') {
                            $('#notification').addClass('alert-success').text('Password verified. Logging you in ..').removeClass('d-none');
                            setTimeout(function() {
                                window.location.href = '/admin';
                            }, 2000);
                        } else {
                            $('#notification').addClass('alert-danger').text('Invalid password').removeClass('d-none');
                        }
                    }, 'json');
                }
            });
        });
    </script>
</body>

</html>