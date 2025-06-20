<?php
// save_profile.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = 'localhost';
$dbname = 'matendo_medics';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $e->getMessage()]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and collect form data
    $fullName = htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ? $_POST['email'] : '';
    $phone = preg_replace('/[^0-9+]/', '', $_POST['phone'] ?? '');
    $location = htmlspecialchars($_POST['location'] ?? '', ENT_QUOTES, 'UTF-8');
    $summary = htmlspecialchars($_POST['summary'] ?? '', ENT_QUOTES, 'UTF-8');
    
    // Handle experience data
    $experience = [];
    if (isset($_POST['experience']) && is_array($_POST['experience'])) {
        foreach ($_POST['experience'] as $exp) {
            $experience[] = [
                'title' => htmlspecialchars($exp['title'] ?? '', ENT_QUOTES, 'UTF-8'),
                'company' => htmlspecialchars($exp['company'] ?? '', ENT_QUOTES, 'UTF-8'),
                'start_date' => htmlspecialchars($exp['start_date'] ?? '', ENT_QUOTES, 'UTF-8'),
                'end_date' => htmlspecialchars($exp['end_date'] ?? '', ENT_QUOTES, 'UTF-8'),
                'current' => isset($exp['current']) ? 1 : 0,
                'responsibilities' => htmlspecialchars($exp['responsibilities'] ?? '', ENT_QUOTES, 'UTF-8')
            ];
        }
    }
    
    // Handle education data
    $education = [];
    if (isset($_POST['education']) && is_array($_POST['education'])) {
        foreach ($_POST['education'] as $edu) {
            $education[] = [
                'degree' => htmlspecialchars($edu['degree'] ?? '', ENT_QUOTES, 'UTF-8'),
                'institution' => htmlspecialchars($edu['institution'] ?? '', ENT_QUOTES, 'UTF-8'),
                'completion_date' => htmlspecialchars($edu['completion_date'] ?? '', ENT_QUOTES, 'UTF-8'),
                'gpa' => htmlspecialchars($edu['gpa'] ?? '', ENT_QUOTES, 'UTF-8')
            ];
        }
    }
    
    // Handle skills and certifications
    $skills = isset($_POST['skills']) ? json_decode($_POST['skills']) : [];
    $certifications = isset($_POST['certifications']) ? json_decode($_POST['certifications']) : [];
    
    // Handle file uploads
    $documents = [];
    if (!empty($_FILES['documents']['name'][0])) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        foreach ($_FILES['documents']['name'] as $key => $name) {
            if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['documents']['tmp_name'][$key];
                $targetFile = $targetDir . basename($name);
                if (move_uploaded_file($tmpName, $targetFile)) {
                    $documents[] = [
                        'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                        'path' => $targetFile,
                        'type' => $_FILES['documents']['type'][$key]
                    ];
                }
            }
        }
    }
    
    // Generate reference number
    $referenceNumber = 'PROF-' . strtoupper(uniqid());
    
    // Prepare and execute SQL query
    $stmt = $pdo->prepare("INSERT INTO professional_profiles (
        full_name, email, phone, location, summary, experience, education,
        skills, certifications, documents, reference_number, submission_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $stmt->execute([
        $fullName, $email, $phone, $location, $summary, 
        json_encode($experience), json_encode($education),
        json_encode($skills), json_encode($certifications), 
        json_encode($documents), $referenceNumber
    ]);
    
    // Return success response
    echo json_encode([
        "success" => true, 
        "message" => "Profile saved successfully",
        "referenceNumber" => $referenceNumber
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>