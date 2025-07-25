<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget<?php if (count($projects_activity) == 0) {
                        echo ' hide';
                    } ?>" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('home_project_activity'); ?>">
    <div class="panel_s projects-activity">
        <div class="panel-body padding-10">
            <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse tw-p-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>


                <span class="tw-text-neutral-700">
                    <?php echo _l('home_project_activity'); ?>
                </span>
            </p>

            <hr class="-tw-mx-3 tw-mt-3 tw-mb-6">

            <div class="activity-feed">
                <?php foreach ($projects_activity as $activity): ?>
                    <?php
                    // Skip processing if source is '1'
                    if ($activity['source'] == '1') {
                        continue;
                    }

                    // Initialize variables
                    $name = '';
                    $href = '';

                    // Determine name based on source
                    if ($activity['source'] == 'stock_import') {
                        $get_staff_full_name = get_staff_by_id_for_dashbord($activity['staff_id']);
                        $name = $get_staff_full_name->firstname . ' ' . $get_staff_full_name->lastname;
                    } else {
                        $name = e($activity['fullname']);
                    }

                    // Determine href based on staff/contact
                    if ($activity['staff_id'] != 0) {
                        $href = admin_url('profile/' . $activity['staff_id']);
                    } elseif ($activity['contact_id'] != 0) {
                        $name = '<span class="label label-info inline-block mright5">' . _l('is_customer_indicator') . '</span> - ' . $name;
                        $href = admin_url('clients/client/' . get_user_id_by_contact_id($activity['contact_id']) . '?contactid=' . $activity['contact_id']);
                    } else {
                        $name = '[CRON]';
                    }
                    ?>

                    <div class="feed-item">
                        <!-- Date section (always shown for non-system activities) -->
                        <div class="date">
                            <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo e(_dt($activity['dateadded'])); ?>">
                                <?php echo e(time_ago($activity['dateadded'])); ?>
                            </span>
                        </div>

                        <div class="text">
                            <p class="bold no-mbot">
                                <?php if (!empty($href)): ?>
                                    <a href="<?php echo e($href); ?>"><?php echo $name; ?></a> -
                                <?php else: ?>
                                    <?php echo $name; ?>
                                <?php endif; ?>

                                <?php
                                // Handle description
                                if (in_array($activity['source'], ['purchase', 'workorder', 'purchase_request', 'payment_certificate', 'stock_import'])) {
                                    echo _l($activity['description']);
                                } else {
                                    echo e($activity['description']);
                                }
                                ?>
                            </p>

                            <?php
                            // Source-specific content
                            switch ($activity['source']) {
                                case 'project':
                                    echo _l('project_name') . ': <a href="' . admin_url('projects/view/' . $activity['project_id']) . '">' . e($activity['project_name']) . '</a>';
                                    break;
                                case 'purchase':
                                    $get_po_by_id = get_pur_order_by_id($activity['rel_id']);
                                    echo _l('purchase_order') . ': <a href="' . admin_url('purchase/purchase_order/' . $get_po_by_id->id) . '">' . e($get_po_by_id->pur_order_number . ' - ' . $get_po_by_id->pur_order_name) . '</a>';
                                    break;
                                case 'workorder':
                                    $get_wo_by_id = get_wo_order_by_id($activity['rel_id']);
                                    echo _l('wo_order') . ': <a href="' . admin_url('purchase/work_order/' . $get_wo_by_id->id) . '">' . e($get_wo_by_id->wo_order_number . ' - ' . $get_wo_by_id->wo_order_name) . '</a>';
                                    break;
                                case 'purchase_request':
                                    $get_pr_by_id = get_pr_order_by_id($activity['rel_id']);
                                    echo _l('pur_request') . ': <a href="' . admin_url('purchase/view_pur_request/' . $get_pr_by_id->id) . '">' . e($get_pr_by_id->pur_rq_code . ' - ' . $get_pr_by_id->pur_rq_name) . '</a>';
                                    break;
                                case 'payment_certificate':
                                    $get_payment_certificate_by_id = get_payment_certificate_by_id($activity['rel_id']);
                                    $_data = '';

                                    if (!empty($get_payment_certificate_by_id->po_id)) {
                                        $get_po_by_id = get_pur_order_by_id($get_payment_certificate_by_id->po_id);
                                        $_data = '<a href="' . admin_url('purchase/payment_certificate/' . $get_payment_certificate_by_id->po_id . '/' . $get_payment_certificate_by_id->id . '/1') . '" target="_blank">' . e($get_po_by_id->pur_order_number . ' - ' . $get_po_by_id->pur_order_name) . '</a>';
                                    } elseif (!empty($get_payment_certificate_by_id->wo_id)) {
                                        $get_wo_by_id = get_wo_order_by_id($get_payment_certificate_by_id->wo_id);
                                        $_data = '<a href="' . admin_url('purchase/wo_payment_certificate/' . $get_payment_certificate_by_id->wo_id . '/' . $get_payment_certificate_by_id->id . '/1') . '" target="_blank">' . e($get_wo_by_id->wo_order_number . ' - ' . $get_wo_by_id->wo_order_name) . '</a>';
                                    }

                                    echo _l('payment_certificate') . ': ' . $_data;
                                    break;
                                case 'stock_import':
                                    $get_good_recipt = get_goods_receipt_by_id($activity['rel_id']);
                                    echo _l('stock_import_new') . ': <a href="' . admin_url('warehouse/manage_purchase/' . $get_good_recipt->id) . '">' . e($get_good_recipt->goods_receipt_code) . '</a>';
                                    break;
                                case 'delivery':
                                    $get_good_delivery = get_goods_delivery_by_id($activity['rel_id']);
                                    echo _l('stock_export_new') . ': <a href="' . admin_url('warehouse/manage_delivery/' . $get_good_delivery->id) . '">' . e($get_good_delivery->goods_delivery_code) . '</a>';
                                    break;
                            }
                            ?>
                        </div>

                        <?php if ($activity['source'] == 'project' && !empty($activity['additional_data'])): ?>
                            <p class="text-muted mtop5"><?php echo $activity['additional_data']; ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>