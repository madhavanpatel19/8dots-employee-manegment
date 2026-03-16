<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION['emp_id'])) {
    echo "<script>window.open('emp-login.php','_self')</script>";
} else {
    $emp_id = $_SESSION['emp_id'];
    $emp_name = $_SESSION['emp_name'];
?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>8dots - Employee Dashboard</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <link href="css/dashboard.css" rel="stylesheet">
        <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            /* Smooth transitions for dashboard panels */
            .panel {
                transition: all 0.3s ease;
            }

            .panel:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }
        </style>
    </head>

    <body>
        <div id="wrapper">
            <?php include("includes/emp_sidebar.php"); ?>
            <div id="page-wrapper">
                <div class="container-fluid">
                    <?php
                    if (isset($_GET['dashboard'])) {
                        include("emp_dashboard.php");
                    } elseif (isset($_GET['worksheet'])) {
                        $_GET['partial'] = true; // Flag for worksheet.php
                        include("worksheet.php");
                    } elseif (isset($_GET['leave_application'])) {
                        $_GET['partial'] = true; // Flag for leave_application.php
                        include("leave_application.php");
                    } elseif (isset($_GET['emp_salary_slip'])) {
                        $_GET['partial'] = true; // Flag for emp_salary_slip.php
                        include("emp_salary_slip.php");
                    } elseif (isset($_GET['view_announcement'])) {
                        include("view_announcement.php");
                    } else {
                        include("emp_dashboard.php");
                    }
                    ?>
                </div>
            </div>
        </div>
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script>
            /* Ask notification permission & register service worker */
            document.addEventListener("DOMContentLoaded", function() {
                if ("Notification" in window) {
                    if (Notification.permission !== "granted") {
                        Notification.requestPermission();
                    }
                }
                
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.register('sw.js').then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }).catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
                }

                /* check announcement every 5 seconds */
                setInterval(checkAnnouncement, 5000);
            });

            /* notification popup */
            function showNotification(title, message) {
                // Play notification sound
                var audio = new Audio('https://commondatastorage.googleapis.com/codeskulptor-assets/week7-bounce.m4a');
                audio.play().catch(function(error) {
                    console.log("Audio play failed:", error);
                });

                // OS Desktop Notification
                if (Notification.permission === "granted") {
                    navigator.serviceWorker.ready.then(function(registration) {
                        registration.showNotification(title, {
                            body: message,
                            icon: "https://cdn-icons-png.flaticon.com/512/1827/1827392.png",
                            requireInteraction: true
                        });
                    }).catch(function() {
                        // Fallback to classic if SW fails
                        var notification = new Notification(title, {
                            body: message,
                            icon: "https://cdn-icons-png.flaticon.com/512/1827/1827392.png",
                            requireInteraction: true
                        });
                        notification.onclick = function() {
                            window.focus();
                            this.close();
                        };
                    });
                }

                // In-App Toast Notification (Guarantees visual display on PC)
                var toast = document.createElement('div');
                toast.style.position = 'fixed';
                toast.style.top = '20px';
                toast.style.right = '20px';
                toast.style.backgroundColor = '#4caf50'; // Green success color
                toast.style.color = '#fff';
                toast.style.padding = '15px 20px';
                toast.style.borderRadius = '5px';
                toast.style.zIndex = '99999';
                toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.3)';
                toast.style.minWidth = '250px';
                toast.style.fontFamily = 'Arial, sans-serif';
                
                toast.innerHTML = '<strong style="font-size:16px;">🔔 ' + title + '</strong><br><span style="font-size:14px;">' + message + '</span>';
                
                document.body.appendChild(toast);
                
                setTimeout(function() {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.5s ease-in-out';
                    setTimeout(function() {
                        toast.remove();
                    }, 500);
                }, 7000); // Remove after 7 seconds
            }

            /* ajax check */
            function checkAnnouncement() {
                fetch("check_announcement.php")
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "new") {
                            showNotification("New Announcement", data.message || data.title);
                        }
                    })
                    .catch(error => console.log('Error checking announcements:', error));
            }
        </script>
    </body>

    </html>
<?php } ?>