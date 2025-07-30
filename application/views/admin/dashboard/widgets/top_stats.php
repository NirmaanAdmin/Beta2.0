<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
.n_width {
  width: 25% !important;
}
.dashboard_stat_title {
  font-size: 18px;
  font-weight: bold;
}
.dashboard_stat_value {
  font-size: 18px;
}
</style>
<div class="widget relative" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('quick_stats'); ?>">
    <?php
      $total_invoices                          = total_rows(db_prefix() . 'invoices', 'status NOT IN (5,6)' . (staff_cant('view', 'invoices') ? ' AND ' . get_invoices_where_sql_for_staff(get_staff_user_id()) : ''));
      $total_invoices_awaiting_payment         = total_rows(db_prefix() . 'invoices', 'status NOT IN (2,5,6)' . (staff_cant('view', 'invoices') ? ' AND ' . get_invoices_where_sql_for_staff(get_staff_user_id()) : ''));
      $percent_total_invoices_awaiting_payment = $total_invoices > 0 ? (($total_invoices_awaiting_payment * 100) / $total_invoices) : 0;
      $percent_total_invoices_awaiting_payment = number_format($percent_total_invoices_awaiting_payment > 0 && $percent_total_invoices_awaiting_payment < 1 ? ceil($percent_total_invoices_awaiting_payment) : $percent_total_invoices_awaiting_payment, 2);
      $_where = '';
      $project_status = get_project_status_by_id(2);
      if (staff_cant('view', 'projects')) {
        $_where = 'id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
      }
      $total_projects = total_rows(db_prefix() . 'projects', $_where);
      $where = ($_where == '' ? '' : $_where . ' AND ') . 'status = 2';
      $total_projects_in_progress = total_rows(db_prefix() . 'projects', $where);
      $_where = '';
      if (staff_cant('view', 'tasks')) {
        $_where = db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ')';
      }
      $total_tasks = total_rows(db_prefix() . 'tasks', $_where);
      $where = ($_where == '' ? '' : $_where . ' AND ') . 'status != ' . Tasks_model::STATUS_COMPLETE;
      $total_not_finished_tasks = total_rows(db_prefix() . 'tasks', $where);
    ?>
    <div class="row">
     <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
       <div class="top_stats_wrapper">
         <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
           <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
             <span class="tw-truncate dashboard_stat_title">Invoices Awaiting Payment</span>
           </div>
           <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
         </div>
         <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
           <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
             <span class="tw-truncate dashboard_stat_value"><?php echo e($total_invoices_awaiting_payment); ?> / <?php echo e($total_invoices); ?></span>
           </div>
           <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
         </div>
       </div>
     </div>
     <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
       <div class="top_stats_wrapper">
         <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
           <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
             <span class="tw-truncate dashboard_stat_title">Projects In Progress</span>
           </div>
           <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
         </div>
         <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
           <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
             <span class="tw-truncate dashboard_stat_value"><?php echo e($total_projects_in_progress); ?> / <?php echo e($total_projects); ?></span>
           </div>
           <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
         </div>
       </div>
     </div>
     <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
       <div class="top_stats_wrapper">
         <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
           <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
             <span class="tw-truncate dashboard_stat_title">Tasks Not Finished</span>
           </div>
           <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
         </div>
         <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
           <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
             <span class="tw-truncate dashboard_stat_value"><?php echo e($total_not_finished_tasks); ?> / <?php echo e($total_tasks); ?></span>
           </div>
           <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
         </div>
       </div>
     </div>
     <div class="quick-stats-invoices col-md-3 tw-mb-2 sm:tw-mb-0 n_width">
       <div class="top_stats_wrapper">
         <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">
           <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
             <span class="tw-truncate dashboard_stat_title">Total Vendors</span>
           </div>
           <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
         </div>
         <div class="tw-text-neutral-800 mtop15 tw-flex tw-items-center tw-justify-between">
           <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">
             <span class="tw-truncate dashboard_stat_value total_vendors"></span>
           </div>
           <span class="tw-font-semibold tw-text-neutral-600 tw-shrink-0"></span>
         </div>
       </div>
     </div>
    </div>
</div>