<?php
$interval = $this->input->post('interval');
if (empty($interval)) {
    $interval = 'month';
}
$from_date = $this->input->post('from_date');
$to_date   = $this->input->post('to_date');
$groups = [];
foreach ($photos as $photo) {
    $timestamp = strtotime($photo['uploaded_at']);
    if (!$timestamp) {
        continue;
    }
    if ($interval === 'custom_range') {
        if (!empty($from_date)) {
            $from_timestamp = strtotime($from_date . ' 00:00:00');
            if ($timestamp < $from_timestamp) {
                continue;
            }
        }
        if (!empty($to_date)) {
            $to_timestamp = strtotime($to_date . ' 23:59:59');
            if ($timestamp > $to_timestamp) {
                continue;
            }
        }
    }
    switch ($interval) {
        case 'day':
            $group_key = date('Y-m-d', $timestamp);
            $group_title = date('F d, Y', $timestamp);
            break;
        case 'week':
            $year = date('o', $timestamp);
            $week = date('W', $timestamp);
            $group_key = $year . '-W' . $week;
            $week_start = new DateTime();
            $week_start->setISODate($year, $week);
            $week_end = clone $week_start;
            $week_end->modify('+6 days');
            $group_title = $week_start->format('F d') . ' - ' . $week_end->format('F d, Y');
            break;
        case 'month':
            $group_key = date('Y-m', $timestamp);
            $group_title = date('F Y', $timestamp);
            break;
        case 'custom_range':
            $group_key = date('Y-m-d', $timestamp);
            $group_title = date('F d, Y', $timestamp);
            break;
        default:
            $group_key = date('Y-m', $timestamp);
            $group_title = date('F Y', $timestamp);
            break;
    }
    if (!isset($groups[$group_key])) {
        $groups[$group_key] = [
            'title'     => $group_title,
            'timestamp' => $timestamp,
            'photos'    => [],
        ];
    }
    $groups[$group_key]['photos'][] = $photo;
}
uasort($groups, function ($a, $b) {
    return $b['timestamp'] <=> $a['timestamp'];
});
?>

<?php foreach ($groups as $group) { ?>
    <div class="timeline_month_group">
        <div class="timeline_month_title">
            <?= html_escape($group['title']); ?>
        </div>
        <div class="timeline_grid">
            <?php foreach ($group['photos'] as $photo) { ?>
                <div class="timeline_photo" data-id="<?= (int) $photo['id']; ?>">
                    <div class="timeline_photo_check">
                        <input type="checkbox" class="timeline_check" data-id="<?= (int) $photo['id']; ?>">
                    </div>
                    <img src="<?= html_escape(SITEPHOTOS_TIMELINE_URL_PATH . rawurlencode($photo['file_name'])); ?>" alt="<?= html_escape($photo['title'] ?: $photo['original_name']); ?>">
                    <div class="timeline_photo_info">
                        <div class="timeline_photo_title">
                            <?= html_escape($photo['title'] ?: $photo['original_name']); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>