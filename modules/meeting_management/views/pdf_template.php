<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Meeting Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: left;
        }

        .details-table,
        .description-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details-table,
        .details-table th,
        .details-table td,
        .description-table td {
            border: 1px solid #ddd;
        }

        .details-table th,
        .details-table td {
            padding: 6px;
            text-align: center;
            font-size: 13px;
        }

        .description-table th,
        .description-table td {
            padding: 6px;
            font-size: 13px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-top: 30px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #666;
            padding: 10px 0;
        }

        @media print {

            /* Force browsers to avoid breaking rows */
            .mom-items-table tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Repeat table headers on every printed page */
            thead {
                display: table-header-group;
            }

            /* Ensure images don't overflow */
            .images_w_table {
                max-width: 100px !important;
                height: auto !important;
            }
        }

        /* Non-print styling for consistency */
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        th {
            border: 1px solid #ccc;
            text-align: left;
            padding: 8px;
        }

        .mom-items-table td,
        .mom-items-table th {
            /* border: 1px solid #ddd; */
            padding: 8px;
        }
    </style>
</head>

<body>

    <?php
    $company_logo = get_option('company_logo_dark');
    $logo = '';
    if (!empty($company_logo)) {
        $logo_path = FCPATH . 'uploads/company/' . $company_logo;
        if (file_exists($logo_path)) {
            $image_data = file_get_contents($logo_path);
            $base64 = 'data:image/png;base64,' . base64_encode($image_data);
            $logo = '<div class="logo">
                <img src="' . $base64 . '" width="130" height="100">
            </div>';
            echo $logo;
        }
    }
    ?>

    <h2>Minutes of Meeting</h2>

    <table class="details-table">
        <tr>
            <th style="width: 15%;">Subject</th>
            <td style="width: 40%;"><?php echo $meeting['meeting_title']; ?></td>
            <th style="width: 15%;">Meeting Date & Time</th>
            <td style="width: 40%;"><?php echo date('d-M-y h:i A', strtotime($meeting['meeting_date'])); ?></td>
            <!-- <td style="width: 15%;">Subject</td>
            <td style="width: 30%;"><?php echo $meeting['meeting_title']; ?></td> -->
        </tr>
        <tr>
            <td style="width: 15%;">Minutes by</td>
            <td style="width: 30%;"><?php echo get_staff_full_name($meeting['created_by']); ?></td>
            <th style="width: 15%;">Venue</th>
            <td style="width: 40%;">BGJ site office</td>
        </tr>
        <tr>

            <td style="width: 15%;">MOM No</td>
            <td style="width: 30%;">BIL-MOM-SUR-<?php echo date('dmy', strtotime($meeting['meeting_date'])); ?></td>
            <td style="width: 15%;"></td>
            <td style="width: 30%;"></td>
        </tr>
    </table>

    <table class="details-table">
        <tr>
            <th style="width: 10%;"></th>
            <td style="width: 20%; font-weight: bold;">Company</td>
            <td style="width: 70%; font-weight: bold;">Participant’s Name</td>
        </tr>

        <!-- Row for BIL Company -->
        <tr>
            <td style="width: 10%;">1</td>
            <td style="width: 20%; text-align: left;">BIL</td>
            <td style="width: 70%; text-align: left;">
                <?php
                if (!empty($participants)) {
                    $all_participant = '';
                    foreach ($participants as $participant) {
                        if (!empty($participant['firstname']) || !empty($participant['lastname']) || !empty($participant['email'])) :
                            $all_participant .= $participant['firstname'] . ' ' . $participant['lastname'] . ', ';
                        endif;
                    }
                    $all_participant = rtrim($all_participant, ", ");
                    echo $all_participant;
                }
                ?>
            </td>
        </tr>

        <!-- Rows for Other Participants -->
        <?php
        // Ensure $other_participants is an array
        $other_participants = is_array($other_participants) ? $other_participants : [];

        if (!empty($other_participants)) {
            foreach ($other_participants as $index => $participant) {
                // Extract participant name and company name
                $participant_name = isset($participant['other_participants']) ? htmlspecialchars($participant['other_participants']) : '';
                $company_name = isset($participant['company_names']) ? htmlspecialchars($participant['company_names']) : '';
        ?>
                <tr>
                    <td style="width: 10%;"><?php echo $index + 2; ?></td> <!-- Increment index by 2 to account for the BIL row -->
                    <td style="width: 20%; text-align: left;"><?php echo $company_name; ?></td>
                    <td style="width: 70%; text-align: left;"><?php echo $participant_name; ?></td>
                </tr>
        <?php
            }
        }
        ?>
    </table>

    <h2 class="section-title">Description</h2>

    <table class="mom-items-table items table-main-dpr-edit has-calculations no-mtop">
        <thead>
            <tr>
                <th>#</th>
                <th>Area/Head</th>
                <th>Description</th>
                <th>Decision</th>
                <th>Action</th>
                <th>Action By</th>
                <th>Target Date</th>
                <?php if ($check_attachment) { ?>
                    <th>Attachments</th>
                <?php } ?>

            </tr>
        </thead>
        <tbody class="mom_body">
            <?php
            $sr = 1;
            $prev_area = ''; // Initialize the previous area value

            foreach ($minutes_data as $data) {
                // Process attachments if available
                if (!empty($data['attachments']) && !empty($data['minute_id'])) {
                    $item_base_url = base_url('uploads/meetings/minutes_attachments/' . $data['minute_id'] . '/' . $data['id'] . '/' . $data['attachments']);
                    $full_item_image = '<img class="images_w_table" src="' . $item_base_url . '" alt="' . $data['attachments'] . '" >';
                }

                // Format the target date
                if (!empty($data['target_date'])) {
                    $target_date = date('d M, Y', strtotime($data['target_date']));
                } else {
                    $target_date = '';
                }

                // Compare current area with the previous one.
                // If they match then set $area as an empty string.
                // Otherwise, use the current area's value.
                if ($data['area'] == $prev_area) {
                    $area = '';
                } else {
                    $area = $data['area'];
                }
                // Update the previous area for the next iteration
                $prev_area = $data['area'];
            ?>
                <tr>
                    <td><?php echo $sr++; ?></td>
                    <!-- Use the $area variable so that duplicate areas show as blank -->
                    <td><?php echo $area; ?></td>
                    <td><?php echo $data['description']; ?></td>
                    <td><?php echo $data['decision']; ?></td>
                    <td><?php echo $data['action']; ?></td>
                    <td>
                        <?php echo getStaffNamesFromCSV($data['staff']); ?><br>
                        <?php echo $data['vendor']; ?>
                    </td>
                    <td><?php echo $target_date; ?></td>
                    <?php if ($check_attachment) { ?>
                        <td><?php echo $full_item_image; ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>

    </table>


    <?php if (!empty($tasks)) : ?>
        <?php /*
        <h2 class="section-title">Tasks Overview</h2>
        <table class="details-table">
            <thead>
                <tr>
                    <th>Task Title</th>
                    <th>Assigned To</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task) : ?>
                    <tr>
                        <td><?php echo $task['task_title']; ?></td>
                        <td><?php echo $task['firstname'] . ' ' . $task['lastname']; ?></td>
                        <td><?php echo date('F d, Y', strtotime($task['due_date'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table> */ ?>
    <?php endif; ?>

    <table class="details-table">
        <tr>
            <th style="width: 20%; text-align: left;">Attachments</th>
            <td style="width: 80%; text-align: left;">None</td>
        </tr>
        <tr>
            <th style="width: 20%; text-align: left;">Distribution to</th>
            <td style="width: 80%; text-align: left;">All participants</td>
        </tr>
    </table>

    <p style="font-size: 13px;">If any comments on above minutes, please revert within 48 hours, after which time they will be held valid.</p>

    <!-- Footer Section -->
    <div class="footer">
        <!-- Display the company name dynamically -->
        <p>&copy; <?php echo date('Y'); ?> <?php echo get_option('companyname'); ?>. All Rights Reserved.</p>
    </div>

</body>

</html>