<script>
  var signaturePad;
  var croppedCtx;

  (function($) {
    "use strict";

    var data_send_mail = {};
    <?php if (isset($send_mail_approve)) {
    ?>
      data_send_mail = <?php echo json_encode($send_mail_approve); ?>;
      data_send_mail.rel_id = <?php echo (int)($goods_receipt->id ?? 0) ?>;
      data_send_mail.rel_type = '1';
      data_send_mail.addedfrom = <?php echo (int)($goods_receipt->addedfrom ?? 0) ?>;

      $.get(admin_url + 'warehouse/send_mail', data_send_mail).done(function(response) {
        response = JSON.parse(response);

      }).fail(function(error) {

      });
    <?php } ?>

    // SignaturePad.prototype.toDataURLAndRemoveBlanks = function() {
    //   var canvas = this._ctx.canvas;
    //   // First duplicate the canvas to not alter the original
    //   var croppedCanvas = document.createElement('canvas');
    //   croppedCtx = croppedCanvas.getContext('2d');

    //   croppedCanvas.width = canvas.width;
    //   croppedCanvas.height = canvas.height;
    //   croppedCtx.drawImage(canvas, 0, 0);

    //   // Next do the actual cropping
    //   var w = croppedCanvas.width,
    //     h = croppedCanvas.height,
    //     pix = {
    //       x: [],
    //       y: []
    //     },
    //     imageData = croppedCtx.getImageData(0, 0, croppedCanvas.width, croppedCanvas.height),
    //     x, y, index;

    //   for (y = 0; y < h; y++) {
    //     for (x = 0; x < w; x++) {
    //       index = (y * w + x) * 4;
    //       if (imageData.data[index + 3] > 0) {
    //         pix.x.push(x);
    //         pix.y.push(y);

    //       }
    //     }
    //   }
    //   pix.x.sort(function(a, b) {
    //     return a - b
    //   });
    //   pix.y.sort(function(a, b) {
    //     return a - b
    //   });
    //   var n = pix.x.length - 1;

    //   w = pix.x[n] - pix.x[0];
    //   h = pix.y[n] - pix.y[0];
    //   var cut = croppedCtx.getImageData(pix.x[0], pix.y[0], w, h);

    //   croppedCanvas.width = w;
    //   croppedCanvas.height = h;
    //   croppedCtx.putImageData(cut, 0, 0);

    //   return croppedCanvas.toDataURL();
    // };

    // var canvas = document.getElementById("signature");
    // signaturePad = new SignaturePad(canvas, {
    //   maxWidth: 2,
    //   onEnd: function() {
    //     signaturePadChanged();
    //   }
    // });

    // $('#identityConfirmationForm').submit(function() {
    //   signaturePadChanged();
    // });

    var table_order_tracker;
    if ($('.table-table_manage_actual_goods_receipt').length) {
      table_order_tracker = $('.table-table_manage_actual_goods_receipt').DataTable();
    } else if ($('.table-items-preview').length) {
      table_order_tracker = $('.table-items-preview').DataTable();
    }

    $('body').on('change', '.payment-date-input', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var paymentDate = $(this).val();
      var purchase_tracker = $(this).data('tracker');
      $.post(admin_url + 'warehouse/update_payment_date', {
        id: rowId,
        payment_date: paymentDate,
        purchase_tracker: purchase_tracker
      }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
          alert_float('success', response.message);
          table_order_tracker.ajax.reload(null, false);
        } else {
          alert_float('danger', response.message);
        }
      });
    });

    $('body').on('change', '.est-delivery-date-input', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var estDeliveryDate = $(this).val();
      var purchase_tracker = $(this).data('tracker');
      $.post(admin_url + 'warehouse/update_est_delivery_date', {
        id: rowId,
        est_delivery_date: estDeliveryDate,
        purchase_tracker: purchase_tracker
      }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
          alert_float('success', response.message);
          table_order_tracker.ajax.reload(null, false);
        } else {
          alert_float('danger', response.message);
        }
      });
    });

    $('body').on('change', '.delivery-date-input', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var DeliveryDate = $(this).val();
      var purchase_tracker = $(this).data('tracker');
      $.post(admin_url + 'warehouse/update_delivery_date', {
        id: rowId,
        delivery_date: DeliveryDate,
        purchase_tracker: purchase_tracker
      }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
          alert_float('success', response.message);
          table_order_tracker.ajax.reload(null, false);
        } else {
          alert_float('danger', response.message);
        }
      });
    });

    $('body').on('change', '.remarks-input', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var remarks = $(this).val();
      var purchase_tracker = $(this).data('tracker');
      $.post(admin_url + 'warehouse/update_remarks', {
        id: rowId,
        remarks: remarks,
        purchase_tracker: purchase_tracker
      }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
          alert_float('success', response.message);
          table_order_tracker.ajax.reload(null, false);
        } else {
          alert_float('danger', response.message);
        }
      });
    });

    $('body').on('change', 'input[name="lead_time_days"]', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var lead_time_days = $(this).val();
      var purchase_tracker = $(this).data('tracker');
      $.post(admin_url + 'warehouse/update_lead_time_days', {
        id: rowId,
        lead_time_days: lead_time_days,
        purchase_tracker: purchase_tracker
      }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
          alert_float('success', response.message);
          table_order_tracker.ajax.reload(null, false);
        } else {
          alert_float('danger', response.message);
        }
      });
    });

    $('body').on('change', 'input[name="advance_payment"]', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var advance_payment = $(this).val();
      var purchase_tracker = $(this).data('tracker');
      $.post(admin_url + 'warehouse/update_advance_payment', {
        id: rowId,
        advance_payment: advance_payment,
        purchase_tracker: purchase_tracker
      }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
          alert_float('success', response.message);
          table_order_tracker.ajax.reload(null, false);
        } else {
          alert_float('danger', response.message);
        }
      });
    });

    $('body').on('change', 'input[name="shop_submission"]', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var shop_submission = $(this).val();
      var purchase_tracker = $(this).data('tracker');
      $.post(admin_url + 'warehouse/update_shop_submission', {
        id: rowId,
        shop_submission: shop_submission,
        purchase_tracker: purchase_tracker
      }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
          alert_float('success', response.message);
          table_order_tracker.ajax.reload(null, false);
        } else {
          alert_float('danger', response.message);
        }
      });
    });

    $('body').on('change', 'input[name="shop_approval"]', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var shop_approval = $(this).val();
      var purchase_tracker = $(this).data('tracker');
      $.post(admin_url + 'warehouse/update_shop_approval', {
        id: rowId,
        shop_approval: shop_approval,
        purchase_tracker: purchase_tracker
      }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
          alert_float('success', response.message);
          table_order_tracker.ajax.reload(null, false);
        } else {
          alert_float('danger', response.message);
        }
      });
    });

    $('body').on('change', 'textarea[name="actual_remarks"]', function(e) {
      e.preventDefault();
      var rowId = $(this).data('id');
      var actual_remarks = $(this).val();
      var purchase_tracker = $(this).data('tracker');
      $.post(admin_url + 'warehouse/update_actual_remarks', {
        id: rowId,
        actual_remarks: actual_remarks,
        purchase_tracker: purchase_tracker
      }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
          alert_float('success', response.message);
          table_order_tracker.ajax.reload(null, false);
        } else {
          alert_float('danger', response.message);
        }
      });
    });

    $(document).off('click', '.upload_shop_drawings_attachments')
      .on('click', '.upload_shop_drawings_attachments', function(e) {
        e.preventDefault();

        var rowId = $(this).data('id');
        var input = $(this).closest('.input-group').find('.upload_shop_drawings_files')[0];

        if (!input.files.length) {
          alert_float('warning', "Please select at least one file to upload.");
          return;
        }

        var formData = new FormData();
        // now loop only to append, no logging inside here
        for (var i = 0; i < input.files.length; i++) {
          formData.append('attachments[]', input.files[i]);
        }
        formData.append('id', rowId);
        formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());

        $.ajax({
          url: admin_url + 'warehouse/upload_purchase_tracker_attachments',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false
        }).done(function(response) {
          var res = JSON.parse(response);
          if (res.status) {
            alert_float('success', "Attachments are uploaded successfully.");
            table_order_tracker.ajax.reload(null, false);
          } else {
            alert_float('warning', "Upload failed.");
          }
        }).fail(function() {
          alert_float('warning', "Upload failed.");
      });
    });

    $('[data-toggle="tooltip"]').tooltip({
      html: true
    });


  })(jQuery);


  function signaturePadChanged() {
    "use strict";

    var input = document.getElementById('signatureInput');
    var $signatureLabel = $('#signatureLabel');
    $signatureLabel.removeClass('text-danger');

    if (signaturePad.isEmpty()) {
      $signatureLabel.addClass('text-danger');
      input.value = '';
      return false;
    }

    $('#signatureInput-error').remove();
    var partBase64 = signaturePad.toDataURLAndRemoveBlanks();
    partBase64 = partBase64.split(',')[1];
    input.value = partBase64;
  }


  function signature_clear() {
    "use strict";
    var canvas = document.getElementById("signature");
    var signaturePad = new SignaturePad(canvas, {
      maxWidth: 2,
      onEnd: function() {

      }
    });
    signaturePad.clear();
    $('input[name="signature"]').val('');

  }

  function sign_request(id) {
    "use strict";
    var signature_val = $('input[name="signature"]').val();
    if (signature_val.length > 0) {
      change_request_approval_status(id, 1, true);
      $('.sign_request_class').prop('disabled', true);
      $('.sign_request_class').html('<?php echo _l('wait_text'); ?>');
      $('.clear').prop('disabled', true);
    } else {
      alert_float('warning', '<?php echo _l('please_sign_the_form'); ?>');
      $('.sign_request_class').prop('disabled', false);
      $('.clear').prop('disabled', false);
    }
  }

  function approve_request(id) {
    "use strict";
    change_request_approval_status(id, 1);
  }

  function deny_request(id) {
    "use strict";
    change_request_approval_status(id, -1);
  }

  function change_request_approval_status(id, status, sign_code) {
    "use strict";

    var data = {};
    data.rel_id = id;
    data.rel_type = '1';

    data.approve = status;
    if (sign_code == true) {
      data.signature = $('input[name="signature"]').val();
    } else {
      data.note = $('textarea[name="reason"]').val();
    }
    $.post(admin_url + 'warehouse/approve_request/' + id, data).done(function(response) {
      response = JSON.parse(response);
      if (response.success === true || response.success == 'true') {
        alert_float('success', response.message);
        window.location.reload();
      }
    });
  }

  function send_request_approve(id) {
    "use strict";
    var data = {};
    data.rel_id = <?php echo (int)($goods_receipt->id ?? 0) ?>;
    data.rel_type = '1';
    data.addedfrom = <?php echo (int)($goods_receipt->addedfrom ?? 0) ?>;
    $("body").append('<div class="dt-loader"></div>');
    $.post(admin_url + 'warehouse/send_request_approve', data).done(function(response) {
      response = JSON.parse(response);
      $("body").find('.dt-loader').remove();
      if (response.success === true || response.success == 'true') {
        alert_float('success', response.message);
        window.location.reload();
      } else {
        alert_float('warning', response.message);
        window.location.reload();
      }
    });
  }

  function accept_action() {
    "use strict";
    $('#add_action').modal('show');
  }

  function send_goods_received(id) {
    "use strict";
    $('#additional_goods_received').html('');
    $('#additional_goods_received').append(hidden_input('po_id', id));
    $('#send_goods_received').modal('show');
  }

  function print_qrcodes() {
    "use strict";

    var print_id = $('#commodity_code_ids').val();
    var vendor_name = $('#vendor_name').val() || ""; // Ensure a default empty string
    var pur_order = $('#pur_order_name').val() || ""; // Ensure a default empty string
    var project_name =  $('#project_name').val() || "";
    var commodity_descriptions = $('#commodity_descriptions').val() || "";
    var purchase_id = $('#purchase_id').val() || ""; // Ensure a default empty string
    if (!print_id) {
      alert("Please select a commodity code.");
      return;
    } 

    // Encode parameters
    var encoded_print_id = encodeURIComponent(print_id);
    var encoded_vendor = encodeURIComponent(vendor_name);
    var encoded_pur_order = encodeURIComponent(pur_order);
    var encoded_project_name = encodeURIComponent(project_name);
    var encoded_commodity_descriptions = encodeURIComponent(commodity_descriptions);
    var encoded_purchase_id = encodeURIComponent(purchase_id);
    // Construct URL safely using template literals
    var url = `${admin_url}warehouse/print_qrcodes_pdf/${encoded_print_id}?vendor=${encoded_vendor}&pur_order=${encoded_pur_order}&project_name=${encoded_project_name}&commodity_descriptions=${encoded_commodity_descriptions}&purchase_id=${encoded_purchase_id}&output_type=I`;

    // Redirect to the generated URL
    window.location.href = url;
  }

  function change_production_status(status, id, purchase_tracker) {
    "use strict";
    if (id > 0) {
      $.post(admin_url + 'warehouse/change_production_status/' + status + '/' + id + '/' + purchase_tracker)
        .done(function(response) {
          try {
            response = JSON.parse(response);

            if (response.success) {
              var $statusSpan = $('#status_span_' + id);

              // Remove all status-related classes
              $statusSpan.removeClass('label-danger label-success label-info label-warning label-primary label-purple label-teal label-orange label-green label-defaul label-secondaryt');

              // Add the new class and update content
              if (response.class) {
                $statusSpan.addClass('label-' + response.class);
              }
              if (response.status_str) {
                $statusSpan.html(response.status_str + ' ' + (response.html || ''));
              }

              // Display success message
              alert_float('success', response.mess);
            } else {
              // Display warning message if the operation fails
              alert_float('warning', response.mess);
            }
          } catch (e) {
            console.error('Error parsing server response:', e);
            alert_float('danger', 'Invalid server response');
          }
        })
        .fail(function(xhr, status, error) {
          console.error('AJAX Error:', error);
          alert_float('danger', 'Failed to update status');
        });
    }
  }

  function change_imp_local_status(status, id, purchase_tracker) {
    "use strict";
    if (id > 0) {
      $.post(admin_url + 'warehouse/change_imp_local_status/' + status + '/' + id + '/' + purchase_tracker)
        .done(function(response) {
          try {
            response = JSON.parse(response);

            if (response.success) {
              var $statusSpan = $('#imp_status_span_' + id);

              // Remove all status-related classes
              $statusSpan.removeClass('label-danger label-success label-info label-warning label-primary label-purple label-teal label-orange label-green label-defaul label-secondaryt');

              // Add the new class and update content
              if (response.class) {
                $statusSpan.addClass('label-' + response.class);
              }
              if (response.status_str) {
                $statusSpan.html(response.status_str + ' ' + (response.html || ''));
              }

              // Display success message
              alert_float('success', response.mess);
            } else {
              // Display warning message if the operation fails
              alert_float('warning', response.mess);
            }
          } catch (e) {
            console.error('Error parsing server response:', e);
            alert_float('danger', 'Invalid server response');
          }
        })
        .fail(function(xhr, status, error) {
          console.error('AJAX Error:', error);
          alert_float('danger', 'Failed to update status');
        });
    }
  }

  function change_tracker_status(status, id, purchase_tracker) {
    "use strict";
    if (id > 0) {
      $.post(admin_url + 'warehouse/change_tracker_status/' + status + '/' + id + '/' + purchase_tracker)
        .done(function(response) {
          try {
            response = JSON.parse(response);

            if (response.success) {
              var $statusSpan = $('#tracker_status_span_' + id);

              // Remove all status-related classes
              $statusSpan.removeClass('label-danger label-success label-info label-warning label-primary label-purple label-teal label-orange label-green label-defaul label-secondaryt');

              // Add the new class and update content
              if (response.class) {
                $statusSpan.addClass('label-' + response.class);
              }
              if (response.status_str) {
                $statusSpan.html(response.status_str + ' ' + (response.html || ''));
              }

              // Display success message
              alert_float('success', response.mess);
            } else {
              // Display warning message if the operation fails
              alert_float('warning', response.mess);
            }
          } catch (e) {
            console.error('Error parsing server response:', e);
            alert_float('danger', 'Invalid server response');
          }
        })
        .fail(function(xhr, status, error) {
          console.error('AJAX Error:', error);
          alert_float('danger', 'Failed to update status');
        });
    }
  }
</script>