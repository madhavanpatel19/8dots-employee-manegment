<?php
if (!isset($con)) { include(__DIR__ . '/../../includes/db.php'); }

if(isset($_POST['submit'])){
    $name = mysqli_real_escape_string($con, $_POST['customer_name']);
    $contact = mysqli_real_escape_string($con, $_POST['contact_number']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $month = mysqli_real_escape_string($con, $_POST['service_month']);

    $quality = mysqli_real_escape_string($con, $_POST['service_quality'] ?? '');
    $ontime = mysqli_real_escape_string($con, $_POST['service_on_time'] ?? '');
    $professional = mysqli_real_escape_string($con, $_POST['professionalism'] ?? '');

    $overall = mysqli_real_escape_string($con, $_POST['overall_satisfaction'] ?? '');
    $recommend = mysqli_real_escape_string($con, $_POST['recommend'] ?? '');

    $liked = mysqli_real_escape_string($con, $_POST['liked']);
    $improve = mysqli_real_escape_string($con, $_POST['improvement']);
    $comments = mysqli_real_escape_string($con, $_POST['comments']);

    $rating = mysqli_real_escape_string($con, $_POST['rating'] ?? '0');

    $insert = "INSERT INTO customer_feedback 
    (customer_name, contact_number, email, service_month,
    service_quality, service_on_time, professionalism,
    overall_satisfaction, recommend,
    liked, improvement, comments, rating)
    
    VALUES 
    ('$name','$contact','$email','$month',
    '$quality','$ontime','$professional',
    '$overall','$recommend',
    '$liked','$improve','$comments','$rating')";

    $result = mysqli_query($con, $insert);
    
    // Self-healing: If columns are missing, add them and try again
    if (!$result && mysqli_errno($con) == 1054) { // 1054 is "Unknown column"
        $error_msg = mysqli_error($con);
        if (strpos($error_msg, 'professionalism') !== false || strpos($error_msg, 'overall_satisfaction') !== false) {
            error_log("[" . date('Y-m-d H:i:s') . "] Attempting self-healing for missing columns...\n", 3, "feedback_errors.log");
            
            mysqli_query($con, "ALTER TABLE customer_feedback ADD COLUMN professionalism VARCHAR(100) DEFAULT NULL AFTER service_on_time");
            mysqli_query($con, "ALTER TABLE customer_feedback ADD COLUMN overall_satisfaction VARCHAR(100) DEFAULT NULL AFTER professionalism");
            
            // Re-try the insert
            $result = mysqli_query($con, $insert);
        }
    }

    if($result){
        echo "<script>alert('Feedback Submitted Successfully')</script>";
        echo "<script>window.open('client_feedback_form.php','_self')</script>";
    } else {
        $error = mysqli_error($con);
        echo "<script>alert('Error: " . $error . "')</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Customer Feedback Form</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f8f9fa;
    font-family: Arial, sans-serif;
}

.form-box {
    background: #fff;
    max-width: 800px;
    margin: 30px auto;
    padding: 30px 40px;
    border: 1px solid #ddd;
}

h2 {
    text-align: center;
    margin-bottom: 25px;
}

.section {
    margin-top: 25px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

.section-title {
    font-weight: bold;
    margin-bottom: 15px;
}

label {
    font-weight: 500;
    margin-top: 10px;
}

.radio-group {
    margin-top: 5px;
    margin-bottom: 10px;
}

.radio-group label {
    margin-right: 20px;
    font-weight: normal;
}

textarea {
    resize: none;
}

.star-rating {
    display: inline-flex;
    flex-direction: row-reverse;
    font-size: 28px;
}

.star-rating input {
    display: none;
}

.star-rating label {
    color: #ccc;
    cursor: pointer;
    margin-right: 5px;
}

.star-rating input:checked ~ label {
    color: gold;
}

.star-rating label:hover,
.star-rating label:hover ~ label {
    color: gold;
}   
</style>

</head>

<body>

<div class="form-box">

<h2>Customer Feedback Form – Monthly Service</h2>

<form method="post">

<!-- Customer Details -->
<div class="section">
<div class="section-title">Customer Details</div>

<div class="row">
    <div class="col-md-6">
        <label>Customer Name</label>
        <input type="text" name="customer_name" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label>Contact Number</label>
        <input type="text" name="contact_number" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label>Email Address</label>
        <input type="email" name="email" class="form-control">
    </div>

    <div class="col-md-6">
        <label>Service Month</label>
        <input type="month" name="service_month" class="form-control">
    </div>
</div>
</div>

<!-- Service Feedback -->
<div class="section">
<div class="section-title">Service Feedback</div>

<label>1. How would you rate the quality of service provided?</label>
<div class="radio-group">
    <label><input type="radio" name="service_quality" value="Excellent"> Excellent</label>
    <label><input type="radio" name="service_quality" value="Good"> Good</label>
    <label><input type="radio" name="service_quality" value="Average"> Average</label>
    <label><input type="radio" name="service_quality" value="Poor"> Poor</label>
</div>

<label>2. Was the service completed on time?</label>
<div class="radio-group">
    <label><input type="radio" name="service_on_time" value="Yes"> Yes</label>
    <label><input type="radio" name="service_on_time" value="No"> No</label>
</div>

<label>3. How satisfied are you with our team’s professionalism?</label>
<div class="radio-group">
    <label><input type="radio" name="professionalism" value="Very Satisfied"> Very Satisfied</label>
    <label><input type="radio" name="professionalism" value="Satisfied"> Satisfied</label>
    <label><input type="radio" name="professionalism" value="Neutral"> Neutral</label>
    <label><input type="radio" name="professionalism" value="Unsatisfied"> Unsatisfied</label>
</div>

</div>

<!-- Overall -->
<div class="section">
<div class="section-title">Overall Experience</div>

<label>4. Overall, how satisfied are you with our service?</label>
<div class="radio-group">
    <label><input type="radio" name="overall_satisfaction" value="Very Satisfied"> Very Satisfied</label>
    <label><input type="radio" name="overall_satisfaction" value="Satisfied"> Satisfied</label>
    <label><input type="radio" name="overall_satisfaction" value="Neutral"> Neutral</label>
    <label><input type="radio" name="overall_satisfaction" value="Unsatisfied"> Unsatisfied</label>
</div>

<label>5. Would you recommend our service to others?</label>
<div class="radio-group">
    <label><input type="radio" name="recommend" value="Yes"> Yes</label>
    <label><input type="radio" name="recommend" value="No"> No</label>
</div>

</div>

<!-- Additional -->
<div class="section">
<div class="section-title">Additional Feedback</div>

<label>6. What did you like the most about our service?</label>
<textarea name="liked" class="form-control"></textarea>

<label>7. What can we improve?</label>
<textarea name="improvement" class="form-control"></textarea>

<label>8. Any additional comments or suggestions?</label>
<textarea name="comments" class="form-control"></textarea>

</div>

<!-- Rating -->
<!-- Rating -->
<div class="section">
<div class="section-title">Rating Summary (Optional)</div>

<label>Overall Rating (Out of 5)</label><br>

<div class="star-rating">
    <input type="radio" name="rating" id="star5" value="5">
    <label for="star5">★</label>

    <input type="radio" name="rating" id="star4" value="4">
    <label for="star4">★</label>

    <input type="radio" name="rating" id="star3" value="3">
    <label for="star3">★</label>

    <input type="radio" name="rating" id="star2" value="2">
    <label for="star2">★</label>

    <input type="radio" name="rating" id="star1" value="1">
    <label for="star1">★</label>
</div>

</div>

<br>

<button name="submit" class="btn btn-dark w-100">Submit Feedback</button>

</form>

</div>

</body>
</html>