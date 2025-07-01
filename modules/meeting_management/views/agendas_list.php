<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <h4><?php echo _l('meeting_agenda'); ?></h4>
                  <!-- Correct Create URL with module name -->
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                     <a href="<?php echo admin_url('meeting_management/minutesController/convert_to_minutes'); ?>" class="btn btn-success">
                        <?php echo _l('create_new_minutes'); ?>
                     </a>

                     <div class="form-group" style="max-width: 300px; margin-bottom: 0;">
                        <label for="project_filter"><?php echo _l('filter_by_project'); ?></label>
                        <select name="project_filter" id="project_filter" class="form-control selectpicker" data-live-search="true">
                           <option value=""><?php echo _l('all_projects'); ?></option>
                           <?php foreach ($projects as $project) : ?>
                              <option value="<?php echo $project['id']; ?>" <?php if ($this->input->get('project_filter') == $project['id']) echo 'selected'; ?>>
                                 <?php echo $project['name']; ?>
                              </option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                  </div>


                  <table class="table table-bordered">
                     <thead>
                        <tr>
                           <th><?php echo _l('No'); ?></th>
                           <th><?php echo _l('meeting_title'); ?></th>
                           <th><?php echo _l('project'); ?></th>
                           <th><?php echo _l('meeting_date'); ?></th>
                           <th><?php echo _l('options'); ?></th>
                        </tr>
                     </thead>
                     <tbody id="minutes-tbody">
                        
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<?php init_tail(); ?>
</body>

</html>

<script>
   $(document).ready(function() {
      const selectedProject = $('#project_filter').val();
      view_mom_list(selectedProject);

      $('#project_filter').change(function() {
         const selectedProject = $(this).val();
         view_mom_list(selectedProject);
      });

      function view_mom_list(selectedProject) {
         $.ajax({
            url: '<?php echo admin_url('meeting_management/agendaController/filter_minutes'); ?>',
            type: 'GET',
            data: {
               project_filter: selectedProject
            },
            dataType: 'json',
            success: function(response) {
               updateTableBody(response);
            },
            error: function(xhr, status, error) {
               console.error('Error:', error);
            }
         });
      }

      function updateTableBody(data) {
         const tbody = $('table tbody');
         tbody.empty();

         if (data.length > 0) {
            $.each(data, function(index, agenda) {
               const row = `
                    <tr>
                        <td>${agenda.meeting_title}</td>
                        <td>${agenda.project_name || 'N/A'}</td>
                        <td>${formatDate(agenda.meeting_date)}</td>
                        <td>
                            <a href="<?php echo admin_url('meeting_management/minutesController/index/'); ?>${agenda.id}" class="btn btn-primary"><?php echo _l('edit_converted_metting'); ?></a>
                            <a href="<?php echo admin_url('meeting_management/agendaController/delete/'); ?>${agenda.id}" class="btn btn-danger"><?php echo _l('delete'); ?></a>
                            <a href="<?php echo admin_url('meeting_management/agendaController/view_meeting/'); ?>${agenda.id}" class="btn btn-secondary"><?php echo _l('view_meeting'); ?></a>
                        </td>
                    </tr>
                `;
               tbody.append(row);
            });
         } else {
            tbody.append('<tr><td colspan="4" class="text-center"><?php echo _l("no_agendas_found"); ?></td></tr>');
         }
      }

      function formatDate(dateString) {
         const date = new Date(dateString);
         return date.toLocaleDateString('en-US', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
         });
      }
   });
</script>