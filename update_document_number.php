<?php
// Connect to database
$servername = "localhost";
$username = "u318220648_basilius";
$password = "Nirmaan@1234";
$dbname = "u318220648_basilius";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define mapping arrays from your Excel data
$design_stages = [
    1 => 'DUR',   // Documents Under Review
    2 => 'BRFs',  // Briefs
    3 => 'CON',   // Concept
    4 => 'SD',    // Schematic
    5 => 'DD',    // Design Development
    6 => 'TD',    // Tender Documents
    7 => 'CD',    // Construction Documents
    8 => 'SHD',   // Shop Drawings
    9 => 'AsB'    // As-Built
];

$purpose_codes = [
    'Issued for Information' => 'IFI',
    'Issued for review' => 'IFR',
    'Issued for approval' => 'IFA',
    'Issued for tender' => 'IFT',
    'Issued for construction' => 'IFC'
];

$status_codes = [
    'under_review' => 'URV',
    'released' => 'RLS',
    'released_with_comments' => 'RWC',
    'rejected' => 'RJC'
];

// Discipline mapping from Excel - column H to G
$discipline_codes = [
    '1' => 'ACO',
    '2' => 'ARC',
    '3' => 'AV',
    '4' => 'BMS',
    '5' => 'STR',
    '6' => 'EL',
    '7' => 'ENG',
    '8' => 'FAC',
    '9' => 'FEG',
    '10' => 'FAPA',
    '11' => 'FF',
    '12' => 'FLS',
    '13' => 'FS',
    '14' => 'HVAC',
    '15' => 'ICS',
    '16' => 'ID',
    '17' => 'LD',
    '18' => 'LGT',
    '19' => 'TRA',
    '20' => 'MAT',
    '21' => 'MEC',
    '22' => 'MEP',
    '23' => 'OPS',
    '24' => 'OPL',
    '25' => 'PM',
    '26' => 'QS',
    '27' => 'SNG',
    '28' => 'SLP',
    '29' => 'SDG',
    '30' => 'VT',
    '31' => 'GD',
    '32' => 'EQP'
];

// Get default project name (assuming you have a projects table)
$project_query = "SELECT name FROM tblprojects WHERE id = 1 LIMIT 1";
$project_result = $conn->query($project_query);
$project_name = "PRO"; // default fallback

if ($project_result->num_rows > 0) {
    $project_row = $project_result->fetch_assoc();
    $project_name = $project_row['name'];
}

// Build project code (first 3 letters of project name)
$project_code = strtoupper(substr($project_name, 0, 3));

// Get all documents that need updating (filetype != 'folder')
$sql = "SELECT id, discipline, design_stage, purpose, status FROM tbldms_items WHERE filetype != 'folder' and discipline != '' and design_stage != '' and purpose != '' and status != ''";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $update_count = 0;

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $discipline = $row['discipline'];
        $design_stage = $row['design_stage'];
        $purpose = $row['purpose'];
        $status = $row['status'];

        // Get codes using the standardized abbreviations
        $discipline_code = $discipline_codes[$discipline] ?? 'GEN';
        $design_stage_code = $design_stages[$design_stage] ?? 'GEN';
        $purpose_code = $purpose_codes[$purpose] ?? 'GEN';
        $status_code = $status_codes[$status] ?? 'GEN';

        // Build new document number
        $document_number = implode('-', [
            $project_code,
            $discipline_code,
            $design_stage_code,
            $purpose_code,
            $status_code
        ]);

        // Update the record
        $update_sql = "UPDATE tbldms_items SET document_number = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $document_number, $id);

        if ($stmt->execute()) {
            $update_count++;
            echo "Updated ID {$id}: {$document_number}\n";
        } else {
            echo "Error updating ID {$id}: " . $conn->error . "\n";
        }

        $stmt->close();
    }

    echo "\nSuccessfully updated {$update_count} records.\n";
} else {
    echo "No documents found to update.\n";
}

$conn->close();
