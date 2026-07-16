<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php hooks()->do_action('app_admin_head'); ?>
<div class="row">

    <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-2">
                        <h4><?php echo pur_html_entity_decode($title) ?></h4>
                    </div>
                    <div class="col-md-2 pull-right" style="padding-top: 0px !important;">
                        <a href="<?php echo admin_url('purchase/vendors_portal/add_dpr_vendor'); ?>"
                            class="btn btn-primary pull-right display-block mright5">
                            <i class="fa-regular fa-plus tw-mr-1"></i> Daily Progress Report
                        </a>
                    </div>
                </div>


                <hr class="mtop5">
                <table class="table dt-table">
                    <thead>
                        <th>#</th>
                        <th><?php echo _l('Subject'); ?></th>
                        <th><?php echo _l('Project'); ?></th>
                        <th><?php echo _l('Department'); ?></th>
                        <th><?php echo _l('Created'); ?></th>
                        <th><?php echo _l('Options'); ?></th>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($reports) && count($reports) > 0) {
                            foreach ($reports as $report) { ?>
                                <tr>
                                    <td class="inv_tr"><a href="<?php echo site_url('purchase/vendors_portal/daily_reports/' . $report['id'] . '/' . $report['hash']); ?>"><?php echo pur_html_entity_decode($report['report_code']); ?></a></td>
                                    <td><?php echo pur_html_entity_decode($report['subject']); ?></td>
                                    <td><?php echo pur_html_entity_decode($report['project_name']); ?></td>
                                    <td><?php echo pur_html_entity_decode($report['department_name']); ?></td>
                                    <td><?php echo _dt($report['created_date']); ?></td>
                                    <td>
                                        <a href="<?php echo site_url('purchase/vendors_portal/daily_reports/' . $report['id'] . '/' . $report['hash']); ?>" class="btn btn-info"><?php echo _l('view'); ?></a>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <p class="bold"><?php echo _l('No Records Found'); ?></p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php hooks()->do_action('app_admin_footer'); ?>