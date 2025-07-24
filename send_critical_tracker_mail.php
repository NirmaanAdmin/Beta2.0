<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'u318220648_basilius';
$db_user = 'u318220648_basilius';
$db_pass = 'Nirmaan@1234';

// Email configuration
$mail_from    = 'ask@nirmaan360.com';
$mail_subject = 'Critical Item Reminder: Target Date Reached [TEST]';

// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // 1) Connect to database
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 2) Fetch critical items
    $sql = "
      SELECT id, description, department, staff, vendor
      FROM tblcritical_mom
      WHERE target_date IS NOT NULL
        AND target_date <> ''
        AND target_date <= :today
        AND status = 1
        AND notification_sent = 0
    ";
    $stmt = $pdo->prepare($sql);
    $today = date('Y-m-d');
    $stmt->execute([':today' => $today]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        exit("No critical items found that have reached their target date and are still open.\n");
    }

    // 3) Test mail function first
    $test_headers = [
        "From: {$mail_from}",
        "Reply-To: {$mail_from}",
        "MIME-Version: 1.0",
        "Content-Type: text/plain; charset=UTF-8"
    ];
    $test_headers = implode("\r\n", $test_headers);
    
    $test_mail = mail(
        'pawan.codrity@gmail.com',
        'Mail Function Test',
        'This is a test of the mail() function',
        $test_headers,
        "-f{$mail_from}"
    );
    
    if (!$test_mail) {
        throw new Exception("Basic mail() function test failed. Check your server's mail configuration.");
    }
    echo "Basic mail() function test passed.\n\n";

    // 4) Loop items
    foreach ($items as $item) {
        // [Previous code for building $assignedTo remains the same...]
        $assignedTo = 'Unassigned';
        $parts = [];

        if (!empty($item['staff'])) {
            $ids = array_filter(array_map('trim', explode(',', $item['staff'])));
            if ($ids) {
                $ph = implode(',', array_fill(0, count($ids), '?'));
                $sSql = "SELECT firstname, lastname FROM tblstaff WHERE staffid IN ({$ph})";
                $sStmt = $pdo->prepare($sSql);
                foreach ($ids as $i => $sid) {
                    $sStmt->bindValue($i + 1, $sid, PDO::PARAM_INT);
                }
                $sStmt->execute();
                $names = array_map(function ($r) {
                    return $r['firstname'] . ' ' . $r['lastname'];
                }, $sStmt->fetchAll(PDO::FETCH_ASSOC));
                if ($names) {
                    $parts[] = implode(', ', $names);
                }
            }
        }

        if (!empty($item['vendor'])) {
            $parts[] = $item['vendor'];
        }

        if ($parts) {
            $assignedTo = implode(' and ', $parts);
        }

        // HTML message (simplified for testing)
        $message = "
        <html><body>
            <h3>TEST EMAIL - Critical Item Reminder</h3>
            <p><strong>Item ID:</strong> {$item['id']}</p>
            <p><strong>Description:</strong> {$item['description']}</p>
            <p><strong>Assigned To:</strong> {$assignedTo}</p>
            <p><strong>Original intended recipients:</strong></p>
            <ul>
        ";

        // 5) Get staff emails
        $eSql = "
          SELECT DISTINCT s.email
          FROM tblstaff s
          JOIN tblstaff_departments sd
            ON s.staffid = sd.staffid
          WHERE sd.departmentid = :deptId
            AND s.active = 1
        ";
        $eStmt = $pdo->prepare($eSql);
        $eStmt->execute([':deptId' => $item['department']]);
        $emails = $eStmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($emails)) {
            echo "No active staff found for dept {$item['department']} (item {$item['id']})\n";
            continue;
        }

        // Add recipients to message
        foreach ($emails as $email) {
            $message .= "<li>{$email}</li>";
        }
        $message .= "</ul></body></html>";

        // 6) Send test email
        $headers = [
            "From: {$mail_from}",
            "Reply-To: {$mail_from}",
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "X-Original-Recipient: " . implode(', ', $emails)
        ];
        $headers = implode("\r\n", $headers);

        $sent = mail(
            'pawan.codrity@gmail.com',
            $mail_subject . " - Item {$item['id']}",
            $message,
            $headers,
            "-f{$mail_from}"
        );

        if ($sent) {
            echo "TEST Email successfully sent for item {$item['id']}\n";
            echo "Would normally go to: " . implode(', ', $emails) . "\n\n";
        } else {
            // Get more detailed error information
            $error = error_get_last();
            echo "Failed to send TEST email for item {$item['id']}\n";
            echo "Error: " . ($error['message'] ?? 'Unknown error') . "\n";
            echo "Would normally go to: " . implode(', ', $emails) . "\n\n";
        }
    }
} catch (PDOException $e) {
    echo "DB error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}