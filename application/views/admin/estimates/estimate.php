<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), ['id' => 'estimate-form', 'class' => '_transaction_form estimate-form']);
            if (isset($estimate)) {
                echo form_hidden('isedit');
            }
            ?>
            <div class="col-md-12">
                <h4
                    class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?php echo e( isset($estimate) ? format_estimate_number($estimate) : 'Create New Budget'); ?>
                    </span>
                    <?php echo isset($estimate) ? format_estimate_status($estimate->status) : ''; ?>
                </h4>
                <?php $this->load->view('admin/estimates/estimate_template'); ?>
            </div>
            <?php echo form_close(); ?>
            <?php $this->load->view('admin/invoice_items/item'); ?>
        </div>
    </div>
</div>
</div>
<!-- Miles Stones -->
<div class="modal fade" id="milestone" data-backdrop="static"  tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('estimates/milestone'), ['id' => 'milestone_form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_milestone'); ?></span>
                    <span class="add-title"><?php echo _l('new_milestone'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo form_hidden('estimate_id', $estimate->id); ?>
                        <div id="additional_milestone"></div>
                        <?php echo render_input('name', 'milestone_name'); ?>
                        <?php echo render_date_input('start_date', 'milestone_start_date', _d(date('Y-m-d'))); ?>
                        <?php echo render_date_input('due_date', 'milestone_due_date', ''); ?>
                        <?php echo render_textarea('description', 'milestone_description'); ?>
                        <?php echo render_input('milestone_order', 'project_milestone_order', total_rows(db_prefix() . 'project_timelines', ['estimate_id' => $estimate->id]) + 1, 'number'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Mile stones end -->
<?php init_tail(); ?>
<script>
$(function() {
    validate_estimate_form();
    // Init accountacy currency symbol
    init_currency();
    // Project ajax search
    init_ajax_project_search_by_customer_id();
    // Maybe items ajax search
    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');

    initItemSelect();

    /**
    * Initializes the logic for handling item selection and input events.
    */
    function initItemSelect() {
        // Listen for input events on the search box of specific dropdowns
        $(document).on('input', '.item-select  .bs-searchbox input', function() {
          let tab = $('.detailed-costing-tab.active').attr('id');
          let query = $(this).val(); // Get the user's query
          let $bootstrapSelect = $(this).closest('#' + tab + ' .bootstrap-select'); // Get the parent bootstrap-select wrapper
          let $selectElement = $bootstrapSelect.find('select.item-select'); // Get the associated select element

          // console.log("Target Select Element:", $selectElement); // Debug the target <select> element

          if (query.length >= 3) {
            fetchItems(query, $selectElement); // Fetch items dynamically
          }
        });

        // Handle the change event for the item-select dropdown
        $(document).on('change', '.item-select', function() {
          handleItemChange($(this)); // Handle item selection change
        });
    }

    /**
    * Fetches items dynamically based on the search query and populates the target select element.
    * @param {string} query - The search query entered by the user.
    * @param {jQuery} $selectElement - The select element to populate.
    */

    function fetchItems(query, $selectElement) {
        var admin_url = '<?php echo admin_url(); ?>';
        $.ajax({
          url: admin_url + 'purchase/fetch_items', // Controller method URL
          type: 'GET',
          data: {
            search: query
          },
          success: function(data) {
            // console.log("Raw Response Data:", data); // Debug the raw data

            try {
              let items = JSON.parse(data); // Parse JSON response
              // console.log("Parsed Items:", items); // Debug parsed items

              if ($selectElement.length === 0) {
                console.error("Target select element not found.");
                return;
              }

              // Clear existing options in the specific select element
              $selectElement.empty();

              // Add default "Type to search..." option
              $selectElement.append('<option value="">Type to search...</option>');

              // Get the pre-selected ID if available (from a data attribute or a hidden field)
              let preSelectedId = $selectElement.data('selected-id') || null;

              // Populate the specific select element with new options
              items.forEach(function(item) {
                let isSelected = preSelectedId && item.id === preSelectedId ? 'selected' : '';
                let option = `<option  data-commodity-code="${item.id}" value="${item.id}"> ${item.commodity_code} ${item.description}</option>`;
                // console.log("Appending Option:", option); // Debug each option
                $selectElement.append(option);
              });

              // Refresh the selectpicker to reflect changes
              $selectElement.selectpicker('refresh');

              // console.log("Updated Select Element HTML:", $selectElement.html()); // Debug the final HTML
            } catch (error) {
              console.error("Error Processing Response:", error);
            }
          },
          error: function() {
            console.error('Failed to fetch items.');
          }
        });
    }

    /**
    * Handles the change event for the item-select dropdown.
    * @param {jQuery} $selectElement - The select element that triggered the change.
    */
    function handleItemChange($selectElement) {
        let selectedId = $selectElement.val(); // Get the selected item's ID
        let selectedCommodityCode = $selectElement.find(':selected').data('commodity-code'); // Get the commodity code
        let $inputField = $selectElement.closest('tr').find('input[name="item_code"]'); // Find the associated input field

        if ($inputField.length > 0) {
          $inputField.val(selectedCommodityCode || ''); // Update the input field with the commodity code
          // console.log("Updated Input Field:", $inputField, "Value:", selectedCommodityCode); // Debug input field
        }
    }
});
</script>
<script>
$(function() {
    appValidateForm($("#milestone_form"), {
        name: "required",
        start_date: "required",
        due_date: "required",
    });
    var milestone_form = $("#milestone_form");
    var milestone_start_date = milestone_form.find("#start_date");
    milestone_start_date.on("changed.bs.select", function (e) {
        milestone_form
        .find("#due_date")
        .data("data-date-min-date", milestone_start_date.val());
    });
    $("body").on("shown.bs.modal", "#milestone", function () {
      $("#milestone").find('input[name="name"]').focus();
    });
    $("#milestone").on("hidden.bs.modal", function (event) {
      $("#additional_milestone").html("");
      $('#milestone input[name="due_date"]').val("");
      $('#milestone input[name="name"]').val("");
      $('#milestone input[name="milestone_order"]').val(
        $(".table-milestones tbody tr").length + 1
      );
      $('#milestone textarea[name="description"]').val("");
      $("#milestone .add-title").removeClass("hide");
      $("#milestone .edit-title").removeClass("hide");
    });
    $("body").on(
      "click",
      ".milestone-column .cpicker,.milestone-column .reset_milestone_color",
      function (e) {
        e.preventDefault();
        var color = $(this).data("color");
        var invoker = $(this);
        var milestone_id = invoker
          .parents(".milestone-column")
          .data("col-status-id");
        $.post(admin_url + "estimates/change_milestone_color", {
          color: color,
          milestone_id: milestone_id,
        }).done(function () {
          // Reset color needs reload
          if (color == "") {
            window.location.reload();
          } else {
            var $parent = invoker.parents(".milestone-column");
            $parent.find(".reset_milestone_color").removeClass("hide");
            $parent
              .find(".panel-heading")
              .addClass("color-white")
              .removeClass("task-phase");
            $parent.find(".edit-milestone-phase").addClass("color-white");
          }
        });
      }
    );
    $("body").on("click", ".new-task-to-project-timelines", function (e) {
      e.preventDefault();
      var project_timeline_id = $(this)
        .parents(".milestone-column")
        .data("col-status-id");
      new_task(
        admin_url +
        "tasks/task?rel_type=estimate&rel_id=" +
        estimate_id +
        "&project_timeline_id=" +
        project_timeline_id
      );
      $('body [data-toggle="popover"]').popover("hide");
    });
});
<?php if(isset($estimate)) { ?>
var estimate_id = <?php echo e($estimate->id); ?>;
function new_milestone() {
  $("#milestone").modal("show");
  $("#milestone .edit-title").addClass("hide");
}
milestones_kanban();
function milestones_kanban() {
  init_kanban(
    "estimates/milestones_kanban",
    milestones_kanban_update,
    ".project-milestone",
    445,
    360,
    after_milestones_kanban
  );
}
function milestones_kanban_update(ui, object) {
  if (object === ui.item.parent()[0]) {
    data = {};
    data.order = [];
    data.milestone_id = $(ui.item.parent()[0])
      .parents(".milestone-column")
      .data("col-status-id");
    data.task_id = $(ui.item).data("task-id");
    var tasks = $(ui.item.parent()[0])
      .parents(".milestone-column")
      .find(".task");

    var i = 0;
    $.each(tasks, function () {
      data.order.push([$(this).data("task-id"), i]);
      i++;
    });
    check_kanban_empty_col("[data-task-id]");

    setTimeout(function () {
      $.post(admin_url + "estimates/update_task_milestone", data);
    }, 50);
  }
}
function after_milestones_kanban() {
  $("#kan-ban").sortable({
    helper: "clone",
    item: ".kan-ban-col",
    cancel: ".milestone-not-sortable",
    update: function (event, ui) {
      var uncategorized_is_after = $(ui.item).next(
        'ul.kan-ban-col[data-col-status-id="0"]'
      );

      if (uncategorized_is_after.length) {
        $(this).sortable("cancel");
        return false;
      }

      var data = {};
      data.order = [];
      var status = $(".kan-ban-col");
      var i = 0;

      $.each(status, function () {
        data.order.push([$(this).data("col-status-id"), i]);
        i++;
      });

      $.post(admin_url + "estimates/update_milestones_order", data);
    },
  });

  for (
    var i = -10;
    i < $(".task-phase").not(".color-not-auto-adjusted").length / 2;
    i++
  ) {
    var r = 120;
    var g = 169;
    var b = 56;
    $(".task-phase:eq(" + (i + 10) + ")")
      .not(".color-not-auto-adjusted")
      .css("background", color(r - i * 13, g - i * 13, b - i * 13))
      .css("border", "1px solid " + color(r - i * 12, g - i * 12, b - i * 12));
  }
}
function milestones_switch_view() {
  $("#milestones-table").toggleClass("hide");
  $(".project-milestones-kanban").toggleClass("hide");
  if (!$.fn.DataTable.isDataTable(".table-milestones")) {
    initDataTable(
      ".table-milestones",
      admin_url + "estimates/project_timelines/" + estimate_id
    );
  }
}
function edit_milestone(invoker, id) {
  $("#additional_milestone").append(hidden_input("id", id));
  $('#milestone input[name="name"]').val($(invoker).data("name"));
  $('#milestone input[name="start_date"]').val($(invoker).data("start_date"));
  $('#milestone input[name="due_date"]').val($(invoker).data("due_date"));
  $('#milestone input[name="milestone_order"]').val($(invoker).data("order"));
  $('#milestone textarea[name="description"]').val(
    $(invoker).data("description")
  );
  $("#milestone").modal("show");
  $("#milestone .add-title").addClass("hide");
}
// When marking task as complete if the staff in on project milestones area, remove this task from milestone in case exists
function _maybe_remove_task_from_project_milestone(task_id) {
  var $milestonesTasksWrappers = $(".milestone-column");
  if ($("body").hasClass("project") && $milestonesTasksWrappers.length > 0) {
    if ($("#exclude_completed_tasks").prop("checked") == true) {
      $milestonesTasksWrappers
        .find('[data-task-id="' + task_id + '"]')
        .remove();
    }
  }
}
<?php } ?>
</script>
</body>

</html>
