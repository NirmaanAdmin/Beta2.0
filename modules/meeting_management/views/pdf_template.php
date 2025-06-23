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

        }

        img.images_w_table {
            width: 116px;
            height: 73px;
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

        .mom_body td {
            border: 1px solid #ccc;
        }

        p {
            margin: 0px;
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
            <!-- <td style="width: 15%;">Meeting Link</td>
            <td style="width: 30%;"><?php echo $meeting['meeting_link']; ?></td> -->
        </tr>
        <tr>
            <th style="width: 15%;">Minutes by</th>
            <td style="width: 30%;"><?php echo get_staff_full_name($meeting['created_by']); ?></td>
            <th style="width: 15%;">Venue</th>
            <td style="width: 30%;"><?php echo $meeting['venue']; ?></td>

        </tr>
        <tr>
            <?php
            $project_name = isset($meeting['project_id']) ? get_project_name_by_id($meeting['project_id']) : 'N/A';
            $project_code = 'N/A';

            if ($project_name !== 'N/A') {
                $project_code = substr(trim($project_name), 0, 3); // Get first 3 chars
                $project_code = strtoupper($project_code); // Convert to uppercase
            }
            ?>
            <th style="width: 15%;">MOM No</th>
            <td>BIL-MOM-<?= $project_code ?>-<?php echo date('dmy', strtotime($meeting['meeting_date'])); ?></td>


            <th style="width: 15%;">Project</th>
            <td style="width: 30%;"><?php echo isset($meeting['project_id']) ? get_project_name_by_id($meeting['project_id']) : 'N/A'; ?> </td>
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
                <th style="width: 5%;">No</th>
                <th>
                    <?php
                    if ($meeting['area_head'] == 1) {
                        echo "Area";
                    } elseif ($meeting['area_head'] == 2) {
                        echo "Head";
                    } else {
                        echo "None";
                    }
                    ?>
                </th>
                <?php if ($check_desc) { ?>
                    <th>Description</th>
                <?php } ?>
                <?php if ($check_decision) { ?>
                    <th>Decision</th>
                <?php } ?>
                <?php if ($check_action) { ?>
                    <th>Action</th>
                <?php } ?>
                <?php if ($check_action_by) { ?>
                    <th>Action By</th>
                <?php } ?>
                <?php if ($check_target_date) { ?>
                    <th>Target Date</th>
                <?php } ?>
                <?php if ($check_attachment) { ?>
                    <th>Attachments</th>
                <?php } ?>

            </tr>
        </thead>
        <tbody class="mom_body">
            <?php
            $sr = 1;
            $prev_area = ''; // Initialize the previous area value

            foreach ($minutes_data as $data): ?>
                <?php
                $full_item_image = '';
                // Process attachments if available
                if (!empty($data['attachments']) && !empty($data['minute_id'])) {
                    $item_base_url = base_url('uploads/meetings/minutes_attachments/' . $data['minute_id'] . '/' . $data['id'] . '/' . $data['attachments']);
                    $full_item_image = '<img class="images_w_table" src="' . $item_base_url . '" alt="' . htmlspecialchars($data['attachments']) . '">';
                }

                // Format the target date
                $target_date = !empty($data['target_date']) ? date('d M, Y', strtotime($data['target_date'])) : '';

                // Handle area display logic
                $area = ($data['area'] == $prev_area) ? '' : $data['area'];
                $prev_area = $data['area'];
                ?>

                <?php if (!empty($data['section_break'])): ?>
                    <?php
                    $colspan = 2; // Base columns: serial_no, area,

                    if ($check_desc) $colspan += 1;
                    if ($check_decision) $colspan += 1;
                    if ($check_action) $colspan += 1;
                    if ($check_action_by) $colspan += 1;
                    if ($check_target_date) $colspan += 1;
                    if ($check_attachment) $colspan += 1;
                    ?>
                    <tr>
                        <td colspan="<?php echo $colspan; ?>" style="text-align:center;font-size:18px;font-weight:600">
                            <?php echo $data['section_break']; ?>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td><?php echo $data['serial_no']; ?></td>
                    <td><?php echo $area; ?></td>

                    <?php if ($check_desc): ?>
                        <td><?php echo $data['description']; ?></td>
                    <?php endif; ?>

                    <?php if ($check_decision): ?>
                        <td><?php echo $data['decision']; ?></td>
                    <?php endif; ?>
                    <?php if ($check_action): ?>
                        <td><?php echo $data['action']; ?></td>
                    <?php endif; ?>

                    <?php if ($check_action_by): ?>
                        <td>
                            <?php echo getStaffNamesFromCSV($data['staff']); ?>
                            <?php if (!empty($data['vendor'])): ?>
                                <br><?php echo $data['vendor']; ?>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <?php if ($check_target_date): ?>
                        <td><?php echo $target_date; ?></td>
                    <?php endif; ?>
                    <?php if ($check_attachment): ?>
                        <td><?php echo $full_item_image; ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
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
            <th style="width: 20%; text-align: left;">Additional Note</th>
            <?php
            if ($meeting['additional_note'] != '') {
                $additional_note = $meeting['additional_note'];
            } else {
                $additional_note = 'None';
            }
            ?>
            <td style="width: 80%; text-align: left;"><?= $additional_note ?></td>
        </tr>
        <tr>
            <th style="width: 20%; text-align: left;">Attachments</th>
            <td style="width: 80%; text-align: left;">
                <?php
                if (isset($attachments) && count($attachments) > 0) {

                    foreach ($attachments as $value) {
                        // Construct the full URL for the image using the attachment data.
                        $item_base_url = base_url('uploads/meetings/agenda_meeting/' . $value['rel_id'] . '/' . $value['file_name']);
                        echo '<div class="mbot15 row inline-block full-width">';
                        echo '<img class="images_w_table" src="' . $item_base_url . '" alt="' . $value['file_name'] . '" >';
                        echo '</div>';
                    }
                } else {
                    echo 'None';
                }
                ?>

            </td>
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