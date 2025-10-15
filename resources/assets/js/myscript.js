window.deleteOptions = function (modelName) {
  $(document).on('click', '.item-delete', function (e) {
    var _this = this;
    e.preventDefault();
    var objectId = $(this).data('object-id');
    var url = route('delete_object', {
      objectId: objectId,
      objectType: modelName,
      actionType: 'FORCE_DELETE',
      withTrashed: $(this).data('with-trashed') != undefined ? $(this).data('with-trashed') : 1
    });
    Swal.fire({
      title: LOCALE.AreYouSure,
      text: LOCALE.DeleteMsgForceDelete,
      icon: 'warning',
      html: LOCALE.PermanentlyAction,
      showDenyButton: true,
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light',
        denyButton: 'btn btn-danger waves-effect waves-light'
      },
      buttonsStyling: false,
      confirmButtonText: LOCALE.Delete,
      denyButtonText: LOCALE.Cancel
    }).then(function (result) {
      if (result.value == true) {
        window
          .deleteObject(url)
          .then(function (data) {
            console.log('data1 code : ', data);
            if (data.code == 1) {
              if ($('.table').DataTable().ajax.json()) $('.table').DataTable().ajax.reload();
              else window.location.reload();

              $(_this).closest('tr').remove();
              $(_this).prop('checked', !$(_this).prop('checked'));
              Swal.fire({
                title: data['data'],
                icon: 'success',
                showConfirmButton: false,
                timer: 3000
              });
            } else {
              Swal.fire({
                title: LOCALE.ErrorHappened,
                icon: 'error',
                showConfirmButton: false,
                timer: 3000
              });
            }
          })
          ['catch'](function (err) {
            console.log(err);
          });
      }
    });
  });
  $(document).on('click', '.activate-object', function (e) {
    var _this2 = this;
    e.preventDefault();
    var objectId = $(this).data('object-id');
    var isChecked = $(this).prop('checked');

    var url = route('delete_object', {
      objectId: objectId,
      objectType: modelName,
      actionType: isChecked == false ? 'SOFT_DELETE' : 'RESTORE_DELETED',
      withTrashed: $(this).data('with-trashed') != undefined ? $(this).data('with-trashed') : 1
    });
    Swal.fire({
      title: LOCALE.AreYouSure,
      text: isChecked == false ? LOCALE.DeleteMsg : LOCALE.RestoreMsg,
      icon: 'question',
      showDenyButton: true,
      customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light',
        denyButton: 'btn btn-danger waves-effect waves-light'
      },
      buttonsStyling: false,
      confirmButtonText: isChecked == false ? LOCALE.Delete : LOCALE.Restore,
      denyButtonText: LOCALE.Cancel
    }).then(function (result) {
      if (result.value == true) {
        window
          .deleteObject(url)
          .then(function (data) {
            console.log('data code : ', data);
            if (data.code == 1) {
              if ($('.table').DataTable().ajax.json()) $('.table').DataTable().ajax.reload();
              else window.location.reload();
              $(_this2).closest('tr').toggleClass('table-primary'); // dtTable.DataTable().rows().draw();
              $(_this2).prop('checked', isChecked);
              Swal.fire({
                title: data['data'],
                icon: 'success',
                showConfirmButton: false,
                timer: 3000
              });
            } else {
              Swal.fire({
                title: LOCALE.ErrorHappened,
                icon: 'error',
                showConfirmButton: false,
                timer: 3000
              });
            }
          })
          ['catch'](function (err) {
            console.log(err);
          });
      }
    });
  });
};
window.deleteObject = function (url) {
  return $.ajax({
    url: url,
    type: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      Authorization: 'Bearer ' + $('meta[name="api-token"]').attr('content')
    },
    success: function (response) {
      return response;
    },
    error: function (xhr) {
      return xhr;
    }
    // beforeSend: function() {
    //     $(".loader-back").show();
    // },
    // complete: function() {
    //     $(".loader-back").hide('slow');
    // }
  });
};
