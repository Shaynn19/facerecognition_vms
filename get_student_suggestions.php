<?php
include 'include/dbconnection.php';

if (isset($_GET['query'])) {
    $query = mysqli_real_escape_string($con, $_GET['query']);

    // Read recognized student names from JSON file
    $recognized_students_json = file_get_contents("recognized_students.json");
    $recognized_students = json_decode($recognized_students_json, true);

    // Filter unique student names that match the entered characters
    $unique_students = array_values(array_unique($recognized_students));

    // Filter student names that match the entered characters
    $filtered_students = array_filter($unique_students, function ($student) use ($query) {
        return stripos($student, $query) !== false;
    });

    // Convert filtered students to a simple array
    $suggestions = array_values($filtered_students);

    echo json_encode($suggestions);
}
?>
