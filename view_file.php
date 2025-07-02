<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "matendo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

    $validTypes = ['resume', 'license_doc', 'certifications'];
    if (!$id || !in_array($type, $validTypes)) {
        die("Invalid request");
    }

    $column = $type . '_data';
    $nameColumn = $type . '_name';

    $stmt = $conn->prepare("SELECT $column, $nameColumn FROM healthcare_professionals WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($fileData, $fileName);
    $stmt->fetch();

    if ($fileData) {
        $mime = mime_content_type('data://application/octet-stream;base64,' . base64_encode($fileData));
        header("Content-Type: $mime");
        header("Content-Disposition: inline; filename=\"$fileName\"");
        echo $fileData;
    } else {
        echo "File not found";
    }

    $stmt->close();
}

$conn->close();
?>