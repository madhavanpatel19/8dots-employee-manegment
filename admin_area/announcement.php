<?php
if (!isset($_SESSION['admin_email'])) {
    echo "<script>window.open('login.php','_self')</script>";
} else {
?>

    <div class='row'>
        <div class='col-lg-12'>
            <h1 class='page-header'>
                <i class='fa fa-pencil-square-o'></i> Announcement
                <div style='text-align:right;margin-top:20px;margin-bottom:20px;'>
                    <button class='btn btn-success' data-toggle='modal' data-target='#addWorksheetModal'>
                        <i class='fa fa-plus'></i> Add announcement
                    </button>
                </div>
            </h1>

            <ol class='breadcrumb'>
                <li class='active'>
                    <i class='fa fa-dashboard'></i> Dashboard / Announcement
                </li>
            </ol>
        </div>
    </div>

    <div class='row'>
        <div class='col-lg-12'>
            <div class='panel panel-default'>

                <div class='panel-heading'>
                    <h3 class='panel-title'>
                        <i class='fa fa-list fa-fw'></i> View Announcements
                    </h3>
                </div>

                <div class='panel-body'>

                    <div class='table-responsive'>
                        <table class='table table-bordered table-hover table-striped'>

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $i = 0;

                                $get_announcements = 'SELECT * FROM announcements ORDER BY created_at DESC';
                                $run_announcements = mysqli_query($con, $get_announcements);

                                while ($row_announcements = mysqli_fetch_array($run_announcements)) {

                                    $announcement_id = $row_announcements['id'];
                                    $announcement_title = $row_announcements['title'];
                                    $announcement_message = $row_announcements['message'];
                                    $announcement_date = $row_announcements['created_at'];

                                    $i++;
                                ?>

                                    <tr>

                                        <td><?php echo $i;
                                            ?></td>
                                        <td><?php echo $announcement_title;
                                            ?></td>
                                        <td><?php echo substr($announcement_message, 0, 100);
                                            ?>...</td>
                                        <td><?php echo $announcement_date;
                                            ?></td>

                                        <td>

                                            <a href="index.php?announcement&delete_announcement=<?php echo $announcement_id; ?>" onclick="return confirm('Delete this announcement?')">

                                                <i class='fa fa-trash-o'></i> Delete

                                            </a>

                                        </td>

                                    </tr>

                                <?php }
                                ?>

                            </tbody>

                        </table>
                    </div>

                    <?php

                    if (isset($_GET['delete_announcement'])) {

                        $delete_id = $_GET['delete_announcement'];

                        $delete_query = "DELETE FROM announcements WHERE id='$delete_id'";

                        $run_delete = mysqli_query($con, $delete_query);

                        if ($run_delete) {

                            echo "<script>alert('Announcement deleted successfully')</script>";
                            echo "<script>window.open('index.php?announcement','_self')</script>";
                        }
                    }

                    ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Add Announcement Modal -->

    <div class='modal fade' id='addWorksheetModal' tabindex='-1'>
        <div class='modal-dialog'>
            <div class='modal-content'>

                <form method='POST'>

                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'>&times;
                        </button>
                        <h4 class='modal-title'>
                            <i class='fa fa-bullhorn'></i> Add Announcement
                        </h4>
                    </div>

                    <div class='modal-body'>

                        <div class='form-group'>
                            <label>Announcement Title</label>
                            <input type='text' name='announcement_title' class='form-control' required>
                        </div>

                        <div class='form-group'>
                            <label>Message</label>
                            <textarea name='announcement_message' class='form-control' rows='5' required></textarea>
                        </div>

                    </div>

                    <div class='modal-footer'>
                    
                        <button type='button' class='btn btn-default' data-dismiss='modal'>
                            Cancel
                        </button>

                        <button type='submit' name='submit_announcement' class='btn btn-success'>
                            <i class='fa fa-save'></i> Post Announcement
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        /* Ask notification permission once */

        document.addEventListener('DOMContentLoaded', function() {

            if ('Notification' in window) {

                if (Notification.permission !== 'granted') {

                    Notification.requestPermission();

                }

            }

        });
    </script>

    <?php

    if (isset($_POST['submit_announcement'])) {

        $announcement_title = mysqli_real_escape_string($con, $_POST['announcement_title']);
        $announcement_message = mysqli_real_escape_string($con, $_POST['announcement_message']);

        $insert = "INSERT INTO announcements (title,message,created_at)
VALUES ('$announcement_title','$announcement_message',NOW())";

        $run = mysqli_query($con, $insert);

        if ($run) {

            echo "

<script>

setTimeout(function(){

if(Notification.permission === 'granted'){

new Notification('New Announcement',{

body:'$announcement_title',
icon:'https://cdn-icons-png.flaticon.com/512/1827/1827392.png'

});

}

alert('Announcement posted successfully');

window.location.href='index.php?announcement';

},800);

</script>

";
        }
    }

    ?>

<?php }
?>