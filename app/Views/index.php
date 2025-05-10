<?php
if (! session()->get('logged_in')) {
    header('Location: /');
    exit;
}

$months = [
    'January'   => 1,
    'February'  => 2,
    'March'     => 3,
    'April'     => 4,
    'May'       => 5,
    'June'      => 6,
    'July'      => 7,
    'August'    => 8,
    'September' => 9,
    'October'   => 10,
    'November'  => 11,
    'December'  => 12
];
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
    <link rel="stylesheet" href="<?= base_url("assets/vendors/sweetalert/sweetalert2.min.css") ?>">
    <!-- endinject -->
    <link rel="shortcut icon" href="<?= base_url("assets/images/favicon.png") ?>" />
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_horizontal-navbar.html -->
        <div class="horizontal-menu">
            <nav class="navbar top-navbar col-lg-12 col-12 p-0">
                <div class="container">
                    <div class="navbar-brand-wrapper d-flex align-items-center justify-content-center" style="height: 100px;">
                        <a class="navbar-brand brand-logo" href="index.html"><img src="<?= base_url("assets/images/MSSD_simplified.png") ?>"
                                alt="logo" /></a>
                        <a class="navbar-brand brand-logo-mini" href="index.html"><img src="<?= base_url("assets/images/logo-mini.svg") ?>"
                                alt="logo" /></a>
                        <h4 class="navbar-brand align-self-center text-white">Simple DTR Manager</h4>
                    </div>
                    <div class="navbar-menu-wrapper d-flex align-items-center">
                        <ul class="navbar-nav navbar-nav-right">
                            <li class="nav-item nav-profile dropdown me-0 me-sm-2">
                                <button class="nav-link" id="logout-btn">
                                    <i class="mdi mdi-logout text-primary"></i>
                                    Logout
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="card bg-white">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <h4 class="mt-1 mb-1">Hi, Welcome back!</h4>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-lg btn-primary d-none d-md-block" data-bs-toggle="modal"
                                            data-bs-target="#import-modal">Import</button>
                                        <button class="btn btn-lg btn-success d-none d-md-block" data-bs-toggle="modal"
                                            data-bs-target="#generate-modal">Generate</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- Modals Start -->
    <div class="modal fade" data-bs-backdrop="static" id="progress-modal" tabindex="-1" role="dialog"
        aria-labelledby="progress-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="progress-modal-label">File generation</h5>
                </div>
                <div class="modal-body">
                    <h5 class="text-center">Generating file please wait .. <span class="text-bold" id="progress-text">0%</span></h5>

                    <div class="progress progress-xl">
                        <div class="progress-bar bg-primary" role="progressbar" id="file-gen-progress" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <img src="<?= base_url("assets/images/Spinner-3.gif") ?>" alt="Loading placeholder" class="d-block mx-auto">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="import-modal" tabindex="-1" role="dialog"
        aria-labelledby="import-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="import-modal-label">Import CSV DTR records</h5>
                </div>
                <div class="modal-body">
                    <input class="form-control" type="file" id="file-csv" accept=".csv" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="upload-btn">Submit</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="generate-modal" tabindex="-1" role="dialog"
        aria-labelledby="generate-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generate-modal-label">Generate DTR forms</h5>
                </div>
                <div class="modal-body">
                    <form id="gen-form">
                        <div class="form-group">
                            <label>Employee</label>
                            <select class="form-control form-control-lg" id="emp_id" aria-label="Employee IDs">
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <select class="form-control form-control-lg" id="year" aria-label="Year" required>
                                <option value="" selected disabled>Select year</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Month</label>
                            <select class="form-control form-control-lg" id="month" aria-label="Month" required>
                                <option value="" selected disabled>Select month</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="generate-btn">Generate</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modals End -->
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="<?= base_url("assets/vendors/js/vendor.bundle.base.js") ?>"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="<?= base_url("assets/vendors/sweetalert/sweetalert.min.js") ?>"></script>
    <script src="<?= base_url("assets/vendors/chart.js/chart.umd.js") ?>"></script>
    <script src="<?= base_url("assets/vendors/progressbar.js/progressbar.min.js") ?>"></script>
    <!-- End plugin js for this page -->
    <!-- Custom js for this page-->
    <script src="<?= base_url("assets/js/dashboard.js") ?>"></script>
    <script src="<?= base_url("assets/js/todolist.js") ?>"></script>
    <!-- End custom js for this page-->
    <script>
        function updateProgress(rawTotal, rawCount) {
            var total = parseFloat(rawTotal) || 0;
            var count = parseFloat(rawCount) || 0;

            if (total <= 0) {
                console.error("Invalid total:", total);
                return; // or set defaults
            }

            var pct = (count / total) * 100;
            if (!isFinite(pct)) pct = 0;

            // 4) format as string with 2 decimals
            var pctStr = pct.toFixed(2);

            // 5) update the bar
            $('#file-gen-progress')
                .css('width', pctStr + '%')
                .attr('aria-valuenow', parseFloat(pctStr));

            $('#progress-text')
                .text(pctStr + '%');
        }

        async function makePDF(data, count, total) { // Wrap AJAX call in a Promise
            return new Promise((resolve, reject) => {
                let row = {
                    data: data,
                    count: count,
                    year: $('#year').val(),
                    month: $('#month').val()
                };

                $.post('admin/make-fill-pdf', row, function(res) {
                    // console.log(res.status);
                    if (res.status == 'success') {
                        updateProgress(total, count);
                        resolve();
                    } else {
                        reject();
                    }
                }, 'json');
            });
        }

        async function processData(data) {
            $('#progress-modal').modal('show');

            const months = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ];
            let year = $('#year').val();
            let month = months[$('#month').val() - 1];
            let count = 1;
            let is_two = 1;
            let vars = [];
            for (const row of data) {
                vars.push(row.emp_id);
                if (is_two == 1) {
                    is_two++;
                } else {
                    await makePDF(vars, count, data.length / 2);
                    count++;
                    vars = [];
                    is_two = 1;
                }

            }

            if (vars.length === 1) {
                await makePDF(vars);
            }
            let pdf = 'DTR_' + month + '_' + year + '.pdf';

            $.ajax({
                    url: 'admin/download-pdf',
                    method: 'POST',
                    data: {
                        file: pdf
                    },
                    xhrFields: {
                        responseType: 'blob'
                    } // tell jQuery to get raw binary
                })
                .done(function(blobData) {
                    const blob = new Blob([blobData], {
                        type: 'application/pdf'
                    });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = pdf;
                    document.body.appendChild(link);
                    link.click();
                    URL.revokeObjectURL(link.href);
                    link.remove();
                    $('#progress-modal').modal('hide');
                    updateProgress(100, 0);

                    Swal.fire({
                        title: "Notification",
                        text: "DTR file successfully generated",
                        icon: "success"
                    });

                    $.post('admin/remove-pdf');
                })
                .fail(function(xhr, status) {
                    alert('Download failed: ' + status);
                });

        }

        function fetchYear() {
            $.post('admin/fetch-year', function(res) {
                var html_content = '<option value="" selected disabled>Select year</option>';
                res.forEach(function(row) {
                    // console.log(row.emp_id);
                    html_content += `<option value='${row}'>${row}</option>`;
                });
                $('#year').html(html_content);
            }, 'json');
        }

        function fetchID() {
            $.post('admin/fetch-id', function(res) {
                var html_content = '<option value="" selected>All Employees</option>';
                res.forEach(function(row) {
                    html_content += `<option value='${row.emp_id}'>${row.emp_name}</option>`;
                });
                $('#emp_id').html(html_content);
            }, 'json');
        }

        function fetchMonth(year) {
            const months = [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ];

            $.post('admin/fetch-month', {
                    year: year
                }, // use the same variable name
                function(res) {
                    let html = '<option value="" selected>Select month</option>';

                    // If your PHP is returning an array of primitives (e.g. ['1','2','3'])
                    res.forEach(function(row) {
                        html += `<option value="${row}">${months[row - 1]}</option>`;
                    });

                    $('#month').html(html);
                },
                'json'
            );
        }

        $(function() {
            fetchID();
            fetchYear();
            const headerMap = {
                'Person ID': 'id',
                'Person Name': 'name',
                'Date': 'date',
                'Punch Records': 'punch'
            };

            $('#generate-btn').on('click', function(e) {
                var $form = $('#gen-form');
                let res = [];

                if ($form[0].checkValidity() === false) {
                    e.preventDefault();
                    e.stopPropagation();
                    $form.addClass('was-validated');
                } else {
                    $form.removeClass('was-validated');
                    if ($('#emp_id').val()) {
                        res[0] = {
                            emp_id: $('#emp_id').val(),
                            emp_name: $('#emp_id option:selected').text()
                        };
                        // console.log(res);
                        processData(res);
                    } else {
                        $.post('admin/fetch-id', function(res) {
                            // console.log(res);
                            processData(res);
                        }, 'JSON');
                    }

                    $('#generate-modal').modal('hide');
                }
            });

            $('#upload-btn').on('click', function() {
                const fileInput = $('#file-csv')[0];
                if (!fileInput.files.length) {
                    Swal.fire({
                        title: "Notification",
                        text: 'Please select a CSV file first.',
                        icon: "error"
                    });
                    return;
                }

                Swal.fire({
                    title: 'Please wait...',
                    text: 'Processing the CSV file',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: function() {
                        Swal.showLoading();
                    }
                });
                const file = fileInput.files[0];
                const reader = new FileReader();

                reader.onload = function(evt) {
                    const lines = evt.target.result.trim().split(/\r?\n/);
                    const rawHeaders = lines.shift().split(',').map(h => h.trim());

                    // Ensure all required headers are present
                    const missingHeaders = Object.keys(headerMap).filter(required => !rawHeaders.includes(required));
                    if (missingHeaders.length) {
                        Swal.fire({
                            title: "Notification",
                            text: 'Missing required headers: ' + missingHeaders.join(', '),
                            icon: "error"
                        });
                        return;
                    }

                    // Build index map
                    const indexMap = {};
                    rawHeaders.forEach((h, i) => {
                        if (headerMap[h]) indexMap[headerMap[h]] = i;
                    });

                    const data = lines.reduce((arr, line) => {
                        if (!line) return arr;
                        const cols = line.split(',');
                        const obj = {};
                        Object.keys(indexMap).forEach(key => {
                            obj[key] = (cols[indexMap[key]] || '').trim();
                        });
                        arr.push(obj);
                        return arr;
                    }, []);

                    // POST to your CI endpoint
                    $.post('admin/import-csv', JSON.stringify(data), 'json')
                        .done(function(res) {
                            Swal.fire({
                                title: "Notification",
                                text: "Upload successful",
                                icon: "success"
                            });
                            $('#import-modal').modal('hide');
                            // Reset to no file selected
                            $('#file-csv').val('');
                            fetchID();
                            fetchYear();
                            $('#year').prop('selectedIndex', 0).trigger('change');
                        })
                        .fail(function(xhr) {
                            Swal.fire({
                                title: "Notification",
                                text: 'Error processing JSON: ' + xhr.responseText,
                                icon: "error"
                            });
                        });
                };

                reader.readAsText(file);
            });

            $('#logout-btn').click(function() {
                Swal.fire({
                    title: 'Logout',
                    text: "You will be logged out, continue?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Continue',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post('admin/logout', function(res) {
                            if (res.status == 'success') {

                                Swal.fire({
                                    title: 'Action Confirmed',
                                    text: 'Logging you out, please wait ..',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    didOpen: function() {
                                        Swal.showLoading();
                                    }
                                });

                                setTimeout(function() {
                                    window.location.href = '/';
                                }, 2000);
                            }
                        }, 'json');
                    }
                });
            });

            $('#year').change(function() {
                fetchMonth($(this).val());
            });
        });
    </script>
</body>

</html>