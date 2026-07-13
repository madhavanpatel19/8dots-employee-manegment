<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($con)) {
    include(__DIR__ . '/../../includes/db.php');
}

/* ===============================
   GET DATA
================================*/

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {

    $name = mysqli_real_escape_string($con, $_POST['name']);
    $number = mysqli_real_escape_string($con, $_POST['number']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $position = mysqli_real_escape_string($con, $_POST['position']);
    $start_date = mysqli_real_escape_string($con, $_POST['start_date']);

    $insert_query = "INSERT INTO nda_forms
    (name, number, email, position, start_date)
    VALUES
    ('$name','$number','$email','$position','$start_date')";

    mysqli_query($con, $insert_query);
} else if (isset($_GET['id'])) {

    $id = mysqli_real_escape_string($con, $_GET['id']);

    $get_nda = "SELECT * FROM nda_forms WHERE id='$id'";
    $run_nda = mysqli_query($con, $get_nda);

    $row_nda = mysqli_fetch_array($run_nda);

    if ($row_nda) {

        $name = $row_nda['name'];
        $number = $row_nda['number'];
        $email = $row_nda['email'];
        $position = $row_nda['position'];
        $start_date = $row_nda['start_date'];
    } else {

        die("NDA record not found.");
    }
} else {

    header("Location: ../../index.php?view_nda");
    exit();
}

$date_formatted =
    date("d F Y", strtotime($start_date));

?>

<!DOCTYPE html>
<html>

<head>

    <title>NDA - <?php echo $name; ?></title>

    <link href="../../css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        .nda-container {

            width: 850px;
            margin: 30px auto;
            background: #fff;
            padding: 50px 60px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);

        }

        h1 {

            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;

        }

        .header-info {

            text-align: center;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 30px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;

        }

        .section-title {

            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 6px;

        }

        .section-text {

            font-size: 14px;
            line-height: 1.7;
            text-align: justify;
        }

        .footer {

            margin-top: 60px;
            text-align: center;
            font-size: 13px;
            border-top: 1px solid #ccc;
            padding-top: 10px;

        }

        .sig-area {

            margin-top: 60px;
            font-size: 14px;

        }

        .page-break {

            page-break-before: always;

        }

        @media print {

            body {
                background: #fff;
            }

            .nda-container {

                width: 100%;
                margin: 0;
                box-shadow: none;

            }

            .no-print {
                display: none;
            }

        }
    </style>

</head>

<body>

    <div class="no-print" style="position:fixed;bottom:20px;right:20px;">

        <button onclick="window.print()"
            class="btn btn-primary">

            Print / Save PDF

        </button>
        <a href="../../index.php?view_nda" class="btn btn-default" style="margin-left: 10px;">Back</a>


    </div>

    <div class="nda-container">

        <!-- ================= HEADER ================= -->

        <h1>NON-DISCLOSURE AGREEMENT</h1>

        <div class="header-info">
            <!-- 516,Shivam Trade Centre,near one world West,</br>opp. Saraswati Multispeciality Hospital Bopal,</br>Ahmedabad, Gujarat 380058<br>

+91 83202 11773<br>

info@8dots.in -->

        </div>

        <!-- ================= INTRO ================= -->

        <div class="section-text">

            This Non-Disclosure Agreement (the "Agreement") is made and entered into as of

            <strong>DATE: <?php echo $date_formatted; ?></strong>,

            by and between

            <strong>8dots</strong>

            ("Disclosing Party")

            and

            <strong><?php echo $name; ?></strong>

            ("Receiving Party").

        </div>

        <!-- ================= SECTIONS ================= -->

        <div class="section-title">
            1. Definition of Confidential Information
        </div>

        <div class="section-text">

            For the purposes of this Agreement, "Confidential Information" shall include all data,
            materials, products, specifications, drawings, designs, plans, trade secrets, and other
            information disclosed or sent to the Receiving Party by the Disclosing Party.
        </div>


        <div class="section-title">
            2. Obligations of the Receiving Party
        </div>

        <div class="section-text">
            The Receiving Party agrees to:</br>

            - Hold all Confidential Information in strict confidence and not disclose it to any third
            parties.
            <br>
            - Use the Confidential Information solely for the purpose of performing work for the
            Disclosing Party.
            </br>
            - Not include any work done for the Disclosing Party in their portfolios or share it
            publicly in any form.
        </div>


        <div class="section-title">
            3. Non-Disclosure
        </div>

        <div class="section-text">

            The Receiving Party shall not disclose, publish, or disseminate any Confidential
            Information to any third party without the prior written consent of the Disclosing
            Party.

        </div>


        <div class="section-title">
            4. Non-Use
        </div>

        <div class="section-text">

            The Receiving Party agrees not to use any Confidential Information for their own use
            or for any purpose other than to carry out discussions concerning, and the
            undertaking of, any business relationship between the Receiving Party and the
            Disclosing Party.

        </div>

        <div class="section-title">
            5. Return of Materials
        </div>

        <div class="section-text">

            Upon termination of the business relationship, the Receiving Party agrees to promptly
            return all Confidential Information, including any copies, to the Disclosing Party.

        </div>

        <div class="page-break"></div>

        <div class="section-title">
            6. Non-Solicitation
        </div>

        <div class="section-text">

            The Receiving Party agrees not to communicate directly with any clients of the
            Disclosing Party without prior written consent. Any unauthorized direct
            communication with clients will be considered a breach of this Agreement and may
            result in immediate termination of the job contract and legal action

        </div>

        <div class="section-title">
            7. Term and Termination
        </div>

        <div class="section-text">

            This Agreement shall commence as of the date first written above and shall continue
            in effect until terminated by either party with thirty (30) days written notice. The
            Receiving Party's duty to hold in confidence Confidential Information that was
            disclosed during the term shall remain in effect indefinitely.

        </div>


        <div class="section-title">
            8. Remedies
        </div>

        <div class="section-text">

            The Receiving Party acknowledges that any breach of this Agreement may cause
            irreparable harm to the Disclosing Party. As such, the Disclosing Party shall be entitled
            to seek equitable relief, including injunction and specific performance, in the event of
            any breach or threatened breach of the terms of this Agreement. Such remedies shall
            not be deemed to be the exclusive remedies for a breach of this Agreement but shall
            be in addition to all other remedies available at law or in equity.

        </div>


        <div class="section-title">
            9. Governing Law
        </div>

        <div class="section-text">

            This Agreement shall be governed by and construed in accordance with the laws of
            the State of Gujarat, without regard to its conflict of laws principles.

        </div>


        <div class="section-title">
            10. Miscellaneous
        </div>

        <div class="section-text">

            - This Agreement constitutes the entire agreement between the parties and
            supersedes all prior agreements, understandings, and communications between the
            parties.
            <br>
            - No amendment or modification of this Agreement shall be valid or binding upon the
            parties unless made in writing and signed by both parties.
            <br>
            - If any provision of this Agreement is found to be invalid or unenforceable, the
            remaining provisions shall continue to be valid and enforceable.

        </div>

        <div class="page-break"></div>

        <!-- ================= SIGNATURE ================= -->

        <div class="sig-area">

            <p>

                IN WITNESS WHEREOF, the parties hereto have executed this Non-Disclosure
                Agreement as of the day and year first above written.

            </p>

            <br><br>

            Releasor's Signature ______________________________
            &nbsp;&nbsp;&nbsp;
            Date ________________

            <br><br>

            Print Name: Kamal parmar,
            8dots, India

            <br><br><br><br>

            Recipient's Signature ______________________________
            &nbsp;&nbsp;&nbsp;
            Date ________________

            <br><br>

            Print Name: <?php echo $name; ?>

        </div>

        <!-- ================= FOOTER ================= -->

        <div class="footer">

            8dots<br>
            www.8dots.in |
            091 83202 11773 |
            info@8dots.in | </br>
            516,Shivam Trade Centre,near one world West, opp. Saraswati Multispeciality Hospital Bopal,</br>Ahmedabad, Gujarat 380058<br>


        </div>

    </div>

</body>

</html>