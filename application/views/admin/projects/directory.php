<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Project Directory Modal -->
<div class="modal fade" id="project_dir" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width: 98%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?php echo _l('Add New'); ?></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body invoice-item">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive" style="overflow-x: unset !important;">
                            <?php
                            echo form_open_multipart(admin_url('projects/add_project_directory'), array('id' => 'critical_tracker-form'));
                            
                            ?>
                            <?php echo form_hidden('project_id', $project->id); ?>
                            <table class="table project-dir-items-table items table-main-invoice-edit has-calculations no-mtop">
                                <thead>
                                    <tr>
                                        <th width="1%"></th>
                                        <th>Company/Consultant</th>
                                        <th>Address</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Contact</th>
                                        <th>Email Account</th>
                                        <th width="3%"></th>
                                    </tr>
                                </thead>
                                <tbody class="mom_body">

                                    <?php echo pur_html_entity_decode($project_directory_row); ?>
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-info pull-right"><?php echo _l('Save'); ?></button>
                            </form>
                        </div>
                        <div id="removed-items"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->
<!-- Timesheet Modal End -->
