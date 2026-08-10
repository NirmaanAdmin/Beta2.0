<script>
   var table_debit_notes;
   $(function(){
     table_debit_notes = $('.table-debit-notes');
     var Params = {
      "debit_notes": "[name='debit_notes[]']",
      "vendors": "[name='vendors[]']",
      "statuses": "[name='statuses[]']",
     };
     initDataTable('.table-debit-notes', admin_url+'purchase/debit_notes_table', ['undefined'], [0], Params, [1,'desc']);
     init_debit_note();
     $.each(Params, function(i, obj) {
      $('select' + obj).on('change', function() {
        table_debit_notes.DataTable().ajax.reload();
      });
     });

     $(document).on('click', '.reset_all_filters', function() {
      var filterArea = $('.all_filters');
      filterArea.find('input').val("");
      filterArea.find('select').selectpicker("val", "");
      table_debit_notes.DataTable().ajax.reload();
     });
     $(document).on('change', 'select[name="debit_notes[]"]', function() {
        $('select[name="debit_notes[]"]').selectpicker('refresh');
     });
     $(document).on('change', 'select[name="vendors[]"]', function() {
        $('select[name="vendors[]"]').selectpicker('refresh');
     });
     $(document).on('change', 'select[name="statuses[]"]', function() {
        $('select[name="statuses[]"]').selectpicker('refresh');
     });
  });

   // Init single credit note
  function init_debit_note(id) {
      load_small_table_item(id, '#debit_note', 'debit_note_id', 'purchase/get_debit_note_data_ajax', '.table-debit-notes');
  }

  function bulk_debit_notes_delete() {
    "use strict";
    var print_id = '';
    var rows = $('.table-debit-notes').find('tbody tr');
    $.each(rows, function() {
      var checkbox = $($(this).find('td').eq(0)).find('input');
      if (checkbox.prop('checked') === true) {
          if (print_id !== '') {
              print_id += ','; // Append a comma before adding the next value
          }
          print_id += checkbox.val();
      }
    });
    if (print_id !== '') {
      if (!confirm('Are you sure you want to delete the selected records?')) {
       return false;
      }
      $.post(admin_url + 'purchase/bulk_debit_notes_delete', {
        ids: print_id,
      }).done(function (response) {
         response = JSON.parse(response);
         if (response.success) {
           alert_float('success', response.message);
         } else {
           alert_float('danger', response.message);
         }
         location.reload();
      });
    } else {
      alert_float('danger', 'Please select at least one item from the list');
    }
   }
</script>