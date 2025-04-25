<?php
session_start();
include 'db.php';

// Check if user is logged in and is a recruiter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header("Location: login.php");
    exit();
}

// Get application ID from URL
if (!isset($_GET['application_id'])) {
    header("Location: recruiter_dashboard.php");
    exit();
}

$application_id = $_GET['application_id'];
$recruiter_id = $_SESSION['user_id'];

// Get application details and verify the recruiter owns the job
$stmt = $conn->prepare("SELECT a.resume_path, j.recruiter_id 
                       FROM applications a 
                       JOIN jobs j ON a.job_id = j.id 
                       WHERE a.id = ?");
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();

if (!$application || $application['recruiter_id'] != $recruiter_id) {
    header("Location: recruiter_dashboard.php");
    exit();
}

$resume_path = $application['resume_path'];

// Check if file exists
if (!file_exists($resume_path)) {
    header("Location: recruiter_dashboard.php");
    exit();
}

// Get file extension
$file_extension = strtolower(pathinfo($resume_path, PATHINFO_EXTENSION));

// Set appropriate headers based on file type
if ($file_extension == 'pdf') {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($resume_path) . '"');
} else if (in_array($file_extension, ['doc', 'docx'])) {
    header('Content-Type: application/msword');
    header('Content-Disposition: inline; filename="' . basename($resume_path) . '"');
} else {
    header("Location: recruiter_dashboard.php");
    exit();
}

// Output the file
readfile($resume_path);
exit(); 