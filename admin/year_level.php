<?php

require_once '../conn/conn.php';


$educ = $_GET['id'];

$educ = $conn->real_escape_string($educ);
$year_level = $conn->query("SELECT
gradelevel.id AS grade_id,
gradelevel.year_level AS grade_level,
gradelevel.education_id AS grade_education_id,
educational.id  AS educational_id,
educational.educational_level AS educational_level
FROM gradelevel
INNER JOIN educational ON gradelevel.education_id = educational.id
WHERE gradelevel.education_id = '$educ'");


$year = $year_level->fetch_all(MYSQLI_ASSOC);


// if (empty($year)) {
$options = "<option selected disabled hidden>Please select grade level</option>";
// } else {
// $options = "";
foreach ($year as $level) {
    $options .= "<option value='" . $level['grade_id'] . "'>" . $level['grade_level'] . "</option>";
};
// }



$education_level = [
    '1' => 'Elementary',
    '2' => 'Junior High School',
    '3' => 'Senior High School',
];

$selected_education_level = isset($education_level[$educ]) ? $education_level[$educ] : '';

$disabled_course  = in_array($selected_education_level, ['Elementary', 'Junior High School', 'Senior High School']);


$response = [
    'options' => $options,
    'disabled_course' => $disabled_course
];

echo json_encode($response);
