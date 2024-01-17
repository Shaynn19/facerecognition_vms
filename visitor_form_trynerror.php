<!--today's changes 14/1/2024 4.22pm: dropdown of whotomeet and hidden field -->

<?php
$msg = "";
include 'include/dbconnection.php';

if (isset($_POST['submit'])) {
    // Process form data and insert into the visitor_entries table
    $fullName = $_POST['fullname'];
    $email = $_POST['email'];
    $mobileNumber = $_POST['mobilenumber'];
    $address = $_POST['address'];
    $whomToMeet = $_POST['whomtomeet'];
    $reasonToMeet = $_POST['reasontomeet'];
    $studentName = $_POST['studentName'];

    $query = mysqli_query($con, "INSERT INTO visitor_entries (FullName, Email, MobileNumber, Address, WhomToMeet, ReasonToMeet, StudentName)
                                  VALUES ('$fullName', '$email', '$mobileNumber', '$address', '$whomToMeet', '$reasonToMeet', '$studentName')");

    if ($query) {
        $msg = "Visitors Detail has been added.";
        // Redirect to another page to avoid form resubmission on refresh
        header("Location: success_page.php");
        exit();
    } else {
        $msg = "Something Went Wrong. Please try again";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>VSM Visitors Forms</title>

    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">
    
    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>

    <!-- Custome Script -->
    <script>
        $(document).ready(function () {
            // Function to toggle the visibility of the studentNameField
            function toggleStudentNameField() {
                var selectedValue = $("#whomToMeet").val();
                if (selectedValue === "student") {
                    $("#studentNameField").show();
                } else {
                    $("#studentNameField").hide();
                }
            }

            // Bind the function to the change event of the whomToMeet dropdown
            $("#whomToMeet").change(function () {
                toggleStudentNameField();
            });

            // Call the function on page load to set the initial state
            toggleStudentNameField();

            // Event listener for input changes in the studentName field
            $("#studentName").on("input", function () {
                var query = $(this).val();
            
            // Function to handle the selection from the dropdown
            //function handleDropdownSelection() {
                //var selectedName = $("#suggestionsDropdown").val();
                //$("#studentName").val(selectedName);
            //}

            //$("#studentName").on("input", function () {
                //var query = $(this).val();

                // Make AJAX request to get suggestions
                $.ajax({
                    url: 'get_student_suggestions.php',
                    method: 'GET',
                    data: { query: query },
                    dataType: 'json',
                    success: function (data) {
                        // Populate the suggestions dropdown
                        var suggestionsDropdown = $("#suggestionsDropdown");
                        suggestionsDropdown.empty();

                        $.each(data, function (index, suggestion) {
                            suggestionsDropdown.append($("<option>").text(suggestion));
                        });

                        // Handle the selection from the dropdown
                        //suggestionsDropdown.change(handleDropdownSelection);
                    }
                });
            });
            // Event listener for selecting an option from the suggestions dropdown
            $("#suggestionsDropdown").change(function () {
                // Update the studentName input field with the selected value
                var selectedName = $(this).val();
                $("#studentName").val(selectedName);
            });
            // Event listener for manually changing the studentName field
            $("#studentName").change(function () {
                // Update the selected value in the suggestionsDropdown
                var enteredName = $(this).val();
                $("#suggestionsDropdown").val(enteredName);
    });
        });
    </script>

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <strong>Visitor Details Form</strong>
                    </div>
                    <div class="card-body card-block">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <p style="font-size:16px; color:red" align="center"> <?php if($msg){echo $msg;}  ?> </p>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="text-input" class=" form-control-label">Full Name</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" id="FullName" name="fullname" placeholder="Full Name" class="form-control" required="">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="email-input" class=" form-control-label">Email</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="email" id="Email" name="email" placeholder="Enter Email" class="form-control" required="">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="password-input" class=" form-control-label">Phone Number</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" id="MobileNo" name="mobilenumber" placeholder="Mobile Number" class="form-control" maxlength="10" required="">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="textarea-input" class=" form-control-label">Address</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <textarea name="address" id="Address" rows="9" placeholder="Enter Visitor Address..." class="form-control" required=""></textarea>
                                    </div>
                                </div>
                                <!-- who to meet -->
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="password-input" class=" form-control-label">Whom to Meet</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <select id="whomToMeet" name="whomtomeet" class="form-control" required="">
                                            <option value="staff">Staff</option>
                                            <option value="student">Student</option>
                                        </select>
                                        <!-- <input type="text" id="WhomToMeet" name="whomtomeet" placeholder="Whom to Meet" class="form-control" required=""> -->
                                    </div>
                                </div>
                                <!-- hidden field for the student's name-->
                                <div class="row form-group" id="studentNameField" style="display: none;">
                                    <div class="col col-md-3">
                                        <label for="studentName" class="form-control-label">Student Name</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" id="studentName" name="studentName" placeholder="Student Name" class="form-control" required="">
                                        <!-- Container for auto-suggest dropdown -->
                                        <select id="suggestionsDropdown" class="form-control"></select>
                                    </div>
                                </div>
                            
                                <!-- reason to meet -->
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="password-input" class=" form-control-label">Reason To Meet</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" id="ReasonToMeet" name="reasontomeet" placeholder="Reason To Meet" class="form-control" required="">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <p style="text-align: center;"><button type="submit" name="submit" id="submit" class="btn btn-primary btn-sm">submit</button></p>  
                                </div>
                        </form>
                    </div>
                </div>      
            </div>            
        </div>
    </div>

    <script>
    $(document).ready(function () {
        // Function to toggle the visibility of the studentNameField
        function toggleStudentNameField() {
            var selectedValue = $("#whomToMeet").val();
            if (selectedValue === "student") {
                $("#studentNameField").show();
            } else {
                $("#studentNameField").hide();
            }
        }

        // Bind the function to the change event of the whomToMeet dropdown
        $("#whomToMeet").change(function () {
            toggleStudentNameField();
        });

        // Call the function on page load to set the initial state
        toggleStudentNameField();

        $("#studentName").on("input", function () {
            var query = $(this).val();

            // Make AJAX request to get suggestions
            $.ajax({
                url: 'get_student_suggestions.php',
                method: 'GET',
                data: { query: query },
                dataType: 'json',
                success: function (data) {
                    // Populate the suggestions dropdown
                    var suggestionsDropdown = $("#suggestionsDropdown");
                    suggestionsDropdown.empty();

                    $.each(data, function (index, suggestion) {
                        suggestionsDropdown.append($("<option>").text(suggestion));
                    });
                }
            });
        });
    });
    </script>


    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="vendor/slick/slick.min.js">
    </script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

</body>

</html>
<!-- end document-->

