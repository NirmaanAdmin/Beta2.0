<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">

      <?php echo form_open($this->uri->uri_string(), ['id' => 'client-form']); ?>

      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">

            <h4 class="mbot20">Add New Client</h4>

            <div class="row">

              <!-- Client ID -->
              <div class="col-md-6">
                <?php echo render_input('client_id', 'Client ID'); ?>
              </div>

              <!-- Name -->
              <div class="col-md-6">
                <?php echo render_input('name', 'Name'); ?>
              </div>

              <!-- Phone -->
              <div class="col-md-6">
                <?php echo render_input('phone', 'Phone', '', 'text'); ?>
              </div>

              <!-- Start Date -->
              <div class="col-md-6">
                <?php echo render_date_input('start_date', 'Start Date'); ?>
              </div>

              <!-- Investment -->
              <div class="col-md-6">
                <?php echo render_input('investment', 'Investment', '', 'number'); ?>
              </div>

              <!-- Status -->
              <div class="col-md-6">
                <label>Status</label>
                <select name="status" class="selectpicker" data-width="100%">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
              </div>

              <!-- Referred By -->
              <div class="col-md-6" style="clear: both;">
                <?php echo render_input('refferred_by', 'Referred By'); ?>
              </div>

              <!-- Remarks -->
              <div class="col-md-6">
                <?php echo render_input('remarks', 'Remarks'); ?>
              </div>

            </div>

            <!-- Submit Button -->
            <div class="text-right mtop20">
              <button type="submit" class="btn btn-info">
                Save
              </button>
            </div>

          </div>
        </div>
      </div>

      <?php echo form_close(); ?>

    </div>
  </div>
</div>

<?php init_tail(); ?>
</body>
</html>
