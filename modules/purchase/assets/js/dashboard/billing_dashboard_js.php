<script>
  var report_from = $('input[name="report-from"]');
  var report_to = $('input[name="report-to"]');
  var date_range = $('#date-range');
  (function($) {
    "use strict";

    $(document).ready(function() {
      $('select[name="vendors"], select[name="projects"], select[name="order_tagged_detail[]"]').on('change', function() {
        get_billing_dashboard();
      });

      $(document).on('click', '.reset_all_filters', function() {
        var filterArea = $('.all_filters');
        filterArea.find('input').val("");
        filterArea.find('select').not('select[name="projects"]').selectpicker("val", "");
        get_billing_dashboard();
      });

      $('select[name="year_requisition"]').on('change', function() {
        get_billing_dashboard();
      });

      report_from.on('change', function() {
        var val = $(this).val();
        var report_to_val = report_to.val();
        if (val != '') {
          report_to.attr('disabled', false);
          if (report_to_val != '') {
            get_billing_dashboard();
          }
        } else {
          report_to.attr('disabled', true);
        }
      });

      report_to.on('change', function() {
        var val = $(this).val();
        if (val != '') {
          get_billing_dashboard();
        }
      });

      $('select[name="months-report"]').on('change', function() {
        var val = $(this).val();
        report_to.attr('disabled', true);
        report_to.val('');
        report_from.val('');
        if (val == 'custom') {
          date_range.addClass('fadeIn').removeClass('hide');
          return;
        } else {
          if (!date_range.hasClass('hide')) {
            date_range.removeClass('fadeIn').addClass('hide');
          }
        }
        get_billing_dashboard();
      });

      get_billing_dashboard();
    });

    var budgetedVsActualCategory;

    function get_billing_dashboard() {
      "use strict";

      var data = {
        vendors: $('select[name="vendors"]').val(),
        projects: $('select[name="projects"]').val(),
        order_tagged_detail: $('select[name="order_tagged_detail[]"]').val(),
        report_months: $('select[name="months-report"]').val(),
        report_from: $('input[name="report-from"]').val(),
        report_to: $('input[name="report-to"]').val(),
        year_requisition: $('select[name="year_requisition"]').val()
      };

      $.post(admin_url + 'purchase/dashboard/get_billing_dashboard', data).done(function(response) {
        response = JSON.parse(response);


      });
    }
  })(jQuery);
</script>