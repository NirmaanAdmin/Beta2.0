<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u318220648_basilius');
define('DB_PASS', 'Nirmaan@1234');
define('DB_NAME', 'u318220648_basilius');

// Connect to database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the relationships between rel_type and their corresponding tables/columns
$relTypeMap = [
    'changee_order' => [
        'table' => 'tblco_orders',
        'id_column' => 'id',
        'approve_column' => 'approve_status',
        'approved_value' => 2  // Specific value for approval
    ],
    'drawing' => [
        'table' => 'tbldms_items',
        'id_column' => 'id',
        'approve_column' => 'approve',
        'approved_value' => 1
    ],
    'payment_certificate' => [
        'table' => 'tblpayment_certificate',
        'id_column' => 'id',
        'approve_column' => 'approve_status',
        'approved_value' => 2
    ],
    'pur_order' => [
        'table' => 'tblpur_orders',
        'id_column' => 'id',
        'approve_column' => 'approve_status',
        'approved_value' => 2
    ],
    'purchase_request' => [
        'table' => 'tblpur_request',
        'id_column' => 'id',
        'approve_column' => 'status',
        'approved_values' => [2, 4] // Array of approved values
    ],
    'stock_import' => [
        'table' => 'tblgoods_receipt',
        'id_column' => 'id',
        'approve_column' => 'approval',
        'approved_value' => 1
    ],
    'stock_export' => [
        'table' => 'tblgoods_delivery',
        'id_column' => 'id',
        'approve_column' => 'approval',
        'approved_value' => 1
    ],
    'wo_order' => [
        'table' => 'tblwo_orders',
        'id_column' => 'id',
        'approve_column' => 'approve_status',
        'approved_value' => 2
    ]
];

// Get all tasks that are not completed (status != 5)
$query = "SELECT id, rel_id, rel_type FROM tbltasks WHERE status != 5 and rel_type != '' AND rel_id !='' and rel_type != 'meeting_minutes'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($task = $result->fetch_assoc()) {
        $relType = $task['rel_type'];
        $relId = $task['rel_id'];
        
        // Check if this rel_type is defined in our map
        if (isset($relTypeMap[$relType])) {
            $config = $relTypeMap[$relType];
            $table = $config['table'];
            $idColumn = $config['id_column'];
            $approveColumn = $config['approve_column'];
            
            // Check if the related record is approved
            $checkQuery = "SELECT $approveColumn FROM $table WHERE $idColumn = $relId";
            $checkResult = $conn->query($checkQuery);
            
            if ($checkResult && $checkResult->num_rows > 0) {
                $row = $checkResult->fetch_assoc();
                $isApproved = false;
                
                // Check approval based on configuration
                if (isset($config['approved_values'])) {
                    // For purchase_request which accepts multiple values
                    $isApproved = in_array($row[$approveColumn], $config['approved_values']);
                } else {
                    // For other types with single approved value
                    $isApproved = ($row[$approveColumn] == $config['approved_value']);
                }
                
                if ($isApproved) {
                    // Update task status to completed (status = 5)
                    $updateQuery = "UPDATE tbltasks SET status = 5 WHERE id = " . $task['id'];
                    if ($conn->query($updateQuery)) {
                        echo "Task ID " . $task['id'] . " marked as completed (Related $relType ID $relId is approved)</br>";
                    } else {
                        echo "Error updating task ID " . $task['id'] . ": " . $conn->error . "</br>";
                    }
                }
            }
        } else {
            echo "Unknown rel_type: $relType for task ID " . $task['id'] . "</br>";
        }
    }
} else {
    echo "No pending tasks found.</br>";
}

$conn->close();
?>