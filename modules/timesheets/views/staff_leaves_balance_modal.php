<div class="row">
    <div class="col-md-12">
        <h4><?php echo _l('leaves_balance_for') . ' ' . $staff_name; ?></h4>
        <p class="text-muted"><?php echo _l('year') . ': ' . date('Y'); ?></p>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="30%"><?php echo _l('leave_type'); ?></th>
                        <th width="17%" class="text-center"><?php echo _l('total_leaves'); ?></th>
                        <th width="17%" class="text-center"><?php echo _l('remaining_leaves'); ?></th>
                        <th width="18%" class="text-center"><?php echo _l('accumulated'); ?></th>
                        <th width="18%" class="text-center"><?php echo _l('days_off'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($staff_leaves)) {
                        foreach ($staff_leaves as $leave) {
                            $remain_class = ($leave['remain'] < 0) ? 'text-danger' : 'text-success';
                            ?>
                            <tr>
                                <td><?php echo $leave['type_name']; ?></td>
                                <td class="text-center"><?php echo $leave['total']; ?></td>
                                <td class="text-center <?php echo $remain_class; ?>">
                                    <strong><?php echo $leave['remain']; ?></strong>
                                </td>
                                <td class="text-center"><?php echo $leave['accumulated'] ? $leave['accumulated'] : '0'; ?></td>
                                <td class="text-center"><?php echo $leave['days_off']; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center">' . _l('no_leaves_found') . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($staff_leaves)) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> 
                    <?php echo _l('leaves_balance_note'); ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>