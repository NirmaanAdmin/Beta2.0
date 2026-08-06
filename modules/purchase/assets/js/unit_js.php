<script> 

var unit_type_value = {};
    function new_unit_type(){
      "use strict"
        $('#unit_type').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
        $('#unit_type_id').html('');

        var handsontable_html ='<div id="hot_unit_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }

setTimeout(function(){

  var hotElement1 = document.querySelector('#hot_unit_type');


   var unit_type = new Handsontable(hotElement1, {
    contextMenu: true,
    manualRowMove: true,
    manualColumnMove: true,
    stretchH: 'all',
    autoWrapRow: true,
    rowHeights: 30,
    defaultRowHeight: 100,
    maxRows: 22,
    minRows:9,
    width: '100%',
    height: 330,
    rowHeaders: true,
    autoColumnSize: {
      samplingRatio: 23
    },

    licenseKey: 'non-commercial-and-evaluation',
    filters: true,
    manualRowResize: true,
    manualColumnResize: true,
    allowInsertRow: true,
    allowRemoveRow: true,
    columnHeaderHeight: 40,

    colWidths: [40, 100, 30,30, 30, 140],
    rowHeights: 30,
    rowHeaderWidth: [44],

    columns: [
                {
                  type: 'text',
                  data: 'unit_code'
                },
                 {
                  type: 'text',
                  data: 'unit_name',
                  // set desired format pattern and
                },
                 {
                  type: 'text',
                  data: 'unit_symbol',
                  // set desired format pattern and
                },
                {
                  type: 'numeric',
                  data: 'order',
                },
                {
                  type: 'checkbox',
                  data: 'display',
                  checkedTemplate: 'yes',
                  uncheckedTemplate: 'no'
                },
                {
                  type: 'text',
                  data: 'note',
                },
              
              ],

    colHeaders: [
        "<?php echo _l('unit_code') ?>",
        "<?php echo _l('unit_name') ?>",
        "<?php echo _l('unit_symbol') ?>",
        "<?php echo _l('order') ?>",
        "<?php echo _l('display') ?>",
        "<?php echo _l('note') ?>",
      ],


    data: [{"unit_code":"","unit_name":"","unit_symbol":"","order":"","display":"no","note":""}],

  });
   unit_type_value = unit_type;
  },300);


    }

  function edit_unit_type(invoker,id){
    "use strict";

    var unit_code = $(invoker).data('unit_code');
    var unit_name = $(invoker).data('unit_name');
    var unit_symbol = $(invoker).data('unit_symbol');

    var order = $(invoker).data('order');
    if($(invoker).data('display') == 0){
      var display = 'no';
    }else{
      var display = 'yes';
    }
    var note = $(invoker).data('note');

        $('#unit_type_id').html('');
        $('#unit_type_id').append(hidden_input('id',id));
        $('#unit_type').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');

        var handsontable_html ='<div id="hot_unit_type" class="hot handsontable htColumnHeaders"></div>';
        if($('#add_handsontable').html() != null){
          $('#add_handsontable').empty();

          $('#add_handsontable').html(handsontable_html);
        }else{
          $('#add_handsontable').html(handsontable_html);

        }

    setTimeout(function(){
      var hotElement1 = document.querySelector('#hot_unit_type');

       var unit_type = new Handsontable(hotElement1, {
        contextMenu: true,
        manualRowMove: true,
        manualColumnMove: true,
        stretchH: 'all',
        autoWrapRow: true,
        rowHeights: 30,
        defaultRowHeight: 100,
        maxRows: 1,
        width: '100%',
        height: 130,
        rowHeaders: true,
        autoColumnSize: {
          samplingRatio: 23
        },

        licenseKey: 'non-commercial-and-evaluation',
        filters: true,
        manualRowResize: true,
        manualColumnResize: true,
        columnHeaderHeight: 40,

        colWidths: [40, 100, 30,30, 30, 140],
        rowHeights: 30,
        rowHeaderWidth: [44],

        columns: [
                {
                  type: 'text',
                  data: 'unit_code',
                  readOnly:true,
                },
                 {
                  type: 'text',
                  data: 'unit_name',
                  // set desired format pattern and
                },
                 {
                  type: 'text',
                  data: 'unit_symbol',
                  // set desired format pattern and
                },
                {
                  type: 'numeric',
                  data: 'order',
                },
                {
                  type: 'checkbox',
                  data: 'display',
                  checkedTemplate: 'yes',
                  uncheckedTemplate: 'no'
                },
                {
                  type: 'text',
                  data: 'note',
                },
              
              ],

         colHeaders: [
        "<?php echo _l('unit_code') ?>",
        "<?php echo _l('unit_name') ?>",
        "<?php echo _l('unit_symbol') ?>",
        "<?php echo _l('order') ?>",
        "<?php echo _l('display') ?>",
        "<?php echo _l('note') ?>",
      ],

        data: [{"unit_code":unit_code,"unit_name":unit_name,"unit_symbol":unit_symbol,"order":order,"display":display,"note":note}],

      });
       unit_type_value = unit_type;
      },300);

    }

    function add_unit_type(invoker){
      "use strict";
      var valid_unit_type = $('#hot_unit_type').find('.htInvalid').html();

      if(valid_unit_type){
        alert_float('danger', "<?php echo _l('data_must_number') ; ?>");
      }else{

        $('input[name="hot_unit_type"]').val(unit_type_value.getData());
        $('#add_unit_type').submit(); 

      }
        
    }

    function bulk_unit_delete() {
     "use strict";
     var print_id = '';
     var rows = $('.dt-table').find('tbody tr');
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
       $.post(admin_url + 'purchase/bulk_unit_delete', {
         ids: print_id,
       }).done(function (response) {
         response = JSON.parse(response);
         if (response.success) {
          alert_float('success', response.message);
          location.reload();
         } else {
          alert_float('danger', response.message);
         }
       });
     } else {
       alert_float('danger', 'Please select at least one item from the list');
     }
    }
</script>