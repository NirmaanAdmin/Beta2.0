<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'u318220648_basilius';
$db_user = 'u318220648_basilius';
$db_pass = 'Nirmaan@1234';

// Email configuration
$mail_from = 'ask@nirmaan360.com';
$mail_subject = 'Critical Item Reminder: Target Date Reached';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get current date in YYYY-MM-DD format
    $current_date = date('Y-m-d');

    // Query to find critical items where target_date has passed and status is 1 (Open)
    $sql = "SELECT * FROM tblcritical_mom 
            WHERE target_date IS NOT NULL 
            AND target_date != ''
            AND target_date <= :current_date 
            AND status = 1";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':current_date', $current_date);
    $stmt->execute();

    $critical_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($critical_items) > 0) {
        foreach ($critical_items as $item) {
            if ($item['department'] > 0) {
                // Determine who it's assigned to
                $assigned_to = 'Unassigned'; // Default value

                if (!empty($item['staff']) || !empty($item['vendor'])) {
                    $assignment_parts = [];

                    // Get staff names if assigned (handling multiple staff IDs)
                    if (!empty($item['staff'])) {
                        try {
                            // Convert comma-separated string to array of IDs
                            $staff_ids = explode(',', $item['staff']);
                            $staff_ids = array_map('trim', $staff_ids);
                            $staff_ids = array_filter($staff_ids); // Remove empty values

                            if (!empty($staff_ids)) {
                                // Create placeholders for the IN clause
                                $placeholders = implode(',', array_fill(0, count($staff_ids), '?'));

                                $staff_sql = "SELECT firstname, lastname 
                             FROM tblstaff 
                             WHERE staffid IN ($placeholders)";
                                $staff_stmt = $pdo->prepare($staff_sql);

                                // Bind each value separately
                                foreach ($staff_ids as $key => $staff_id) {
                                    $staff_stmt->bindValue(($key + 1), $staff_id);
                                }

                                $staff_stmt->execute();
                                $staff_members = $staff_stmt->fetchAll(PDO::FETCH_ASSOC);

                                // Format staff names
                                $staff_names = [];
                                foreach ($staff_members as $staff) {
                                    $staff_names[] = $staff['firstname'] . ' ' . $staff['lastname'];
                                }

                                if (!empty($staff_names)) {
                                    $assignment_parts[] = implode(', ', $staff_names);
                                }
                            }
                        } catch (PDOException $e) {
                            // Log error but continue
                            error_log("Error fetching staff names: " . $e->getMessage());
                        }
                    }

                    // Add vendor if assigned (assuming vendor is a single value)
                    if (!empty($item['vendor'])) {
                        $assignment_parts[] = $item['vendor'];
                    }

                    $assigned_to = implode(' and ', $assignment_parts);
                }

                // Prepare email message
               $message = "<html><body>
            <p>This critical item '<a target=\"_blank\" href=\"https://basilius.nirmaan360construction.com/admin/meeting_management/minutesController/critical_agenda?id={$item['id']}\">{$item['description']}</a>' has reached the target date.</p>
            <p>The status is still <strong>Open</strong>. This was assigned to <strong>{$assigned_to}</strong>.</p>
            </body></html>";

                // Get all staff emails for the department
                $email_sql = "SELECT s.email, sd.departmentid
                             FROM tblstaff s
                             JOIN tblstaff_departments sd ON s.staffid = sd.staffid
                             WHERE sd.departmentid = :department_id
                             AND s.active = 1";

                $email_stmt = $pdo->prepare($email_sql);
                $email_stmt->bindParam(':department_id', $item['department']);
                $email_stmt->execute();

                $recipients = $email_stmt->fetchAll(PDO::FETCH_ASSOC);

                // echo '<pre>'; print_r($recipients); 

                if (count($recipients) > 0) {
                    $headers = "From: $mail_from\r\n";
                    $headers .= "Reply-To: $mail_from\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                    // Send to each staff member in the department
                    foreach ($recipients as $recipient) {
                        // $to_email = $recipient['email'];
                        $to_email = 'pawan.codrity@gmail.com';


                        if (mail($to_email, $mail_subject, $message, $headers)) {
                            echo "Email sent for item ID {$item['id']} to {$to_email}\n";
                            // Optional: Log that email was sent to prevent duplicate emails
                            // $log_sql = "UPDATE tblcritical_mom SET reminder_sent = 1 WHERE id = :id";
                            // $log_stmt = $pdo->prepare($log_sql);
                            // $log_stmt->bindParam(':id', $item['id']);
                            // $log_stmt->execute();
                        } else {
                            echo "Failed to send email for item ID {$item['id']} to {$to_email}\n";
                        }
                    }
                } else {
                    echo "No active staff members found in department ID {$item['department']} for item ID {$item['id']}\n";
                }
            }
        }
    } else {
        echo "No critical items found that have reached their target date and are still open.\n";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "General error: " . $e->getMessage() . "\n";
}
