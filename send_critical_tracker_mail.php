<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'u318220648_basilius';
$db_user = 'u318220648_basilius';
$db_pass = 'Nirmaan@1234';

// Email configuration
$mail_from    = 'ask@nirmaan360.com';
$mail_subject = 'Critical Item Reminder: Target Date Reached';

try {
    // 1) Connect
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 2) Fetch every open item whose target_date has passed
    $sql = "
      SELECT id, description, department, staff, vendor
      FROM tblcritical_mom
      WHERE target_date IS NOT NULL
        AND target_date <> ''
        AND target_date <= :today
        AND status = 1
    ";
    $stmt = $pdo->prepare($sql);
    $today = date('Y-m-d');
    $stmt->execute([':today' => $today]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($items)) {
        exit("No critical items found that have reached their target date and are still open.\n");
    }

    // 3) Prepare reusable headers
    $headerLines = [
        "From: {$mail_from}",
        "Reply-To: {$mail_from}",
        "MIME-Version: 1.0",
        "Content-Type: text/html; charset=UTF-8"
    ];
    $headersString = implode("\r\n", $headerLines);

    // 4) Loop items
    foreach ($items as $item) {
        // build a human‑readable “assigned to”
        $assignedTo = 'Unassigned';
        $parts = [];

        // staff
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

        // vendor (if any)
        if (!empty($item['vendor'])) {
            $parts[] = $item['vendor'];
        }

        if ($parts) {
            $assignedTo = implode(' and ', $parts);
        }

        // HTML message
        $message = "
          <html><body>
            <p>This critical item
              '<a target=\"_blank\" href=\"
                https://basilius.nirmaan360construction.com/
                admin/meeting_management/minutesController/
                critical_agenda}\">
                {$item['description']}
              </a>'
              has reached the target date.
            </p>
            <p>
              The status is still <strong>Open</strong>.
              This was assigned to <strong>{$assignedTo}</strong>.
            </p>
          </body></html>
        ";

        // 5) Get *all* active staff emails for this department
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

        // 6) Send one mail per address, *per* item
        foreach ($emails as $to) {
            // build unique headers for each send
            $headers = [
                "From: {$mail_from}",
                "Reply-To: {$mail_from}",
                "MIME-Version: 1.0",
                "Content-Type: text/html; charset=UTF-8",
                // make each Message‑ID unique
                "Message-ID: <" . uniqid('', true) . "@nirmaan360construction.com>",
                // optional: update Date so it's never identical
                "Date: " . date(DATE_RFC2822)
            ];
            $headersString = implode("\r\n", $headers);

            // now send
            $sent = mail(
                'pawan.codrity@gmail.com',
                $mail_subject,
                $message,
                $headersString,
                "-f{$mail_from}"
            );

            echo $sent
                ? "Email sent for item {$item['id']} to {$to}\n"
                : "Failed to send for item {$item['id']} to {$to}\n";
        }
    }
} catch (PDOException $e) {
    echo "DB error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
