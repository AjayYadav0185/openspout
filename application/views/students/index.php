<!DOCTYPE html>
<html>

<head>
  <title>School App</title>


  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>

<body>
  <div class="container mt-4">
    <h3 class="mb-4">School Management</h3>


    <div id="msg"></div>

    <div class="card">
      <div class="card-body">

        <form id="studentForm" class="row g-2 mb-3">

          <input type="hidden" name="id" id="student_id">

          <div class="col">
            <input type="text" name="name" id="name" class="form-control" placeholder="Name" required>
          </div>

          <div class="col">
            <input type="email" name="email" id="email" class="form-control" placeholder="Email">
          </div>

          <div class="col">
            <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone">
          </div>

          <div class="col">
            <button class="btn btn-primary" id="submitBtn">Add</button>
          </div>

        </form>



        <form method="post" action="<?= base_url('students/import') ?>" enctype="multipart/form-data" class="mb-3">
          <input type="file" name="file" required>
          <button class="btn btn-success btn-sm">Import Excel</button>
          <a href="<?= base_url('students/export') ?>" class="btn btn-warning btn-sm">Export Excel</a>
        </form>

        <form id="importForm" enctype="multipart/form-data" class="mb-3">
          <div class="row g-2 align-items-center">

            <div class="col-auto">
              <input type="file" name="file" class="form-control" required>
            </div>

            <div class="col-auto">
              <button class="btn btn-success btn-sm">Import Large Excel</button>
            </div>

            <div class="col-auto">
              <span id="importLoader" style="display:none;" class="text-primary">Uploading...</span>
            </div>

            <div class="col-auto">
              <a href="<?= base_url('students/export_large') ?>" class="btn btn-danger btn-sm">Export Large</a>
            </div>

          </div>
        </form>


        <table class="table table-bordered" id="studentTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

      </div>
    </div>
  </div>


  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <script>
    let table;

    $(document).ready(function() {


      table = $('#studentTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "<?= base_url('students/get_students') ?>",
          type: "POST"
        },
        columns: [{
            data: 'name'
          },
          {
            data: 'email'
          },
          {
            data: 'phone'
          },
          {
            data: 'id',
            render: function(id) {
              return `<button class="btn btn-sm btn-warning editBtn" data-id="${id}">Edit</button>`;
            }
          }
        ],
        pageLength: 5
      });

    });

    $(document).on('click', '.editBtn', function() {

      let id = $(this).data('id');

      $.ajax({
        url: "<?= base_url('students/get_one') ?>/" + id,
        type: "GET",
        dataType: "json",
        success: function(res) {

          $('#student_id').val(res.id);
          $('#name').val(res.name);
          $('#email').val(res.email);
          $('#phone').val(res.phone);

          $('#submitBtn').text('Update').removeClass('btn-primary').addClass('btn-success');
        }
      });

    });

    $('#studentForm').on('submit', function(e) {
      e.preventDefault();

      let id = $('#student_id').val();
      let url = id ?
        "<?= base_url('students/update_ajax') ?>" :
        "<?= base_url('students/store_ajax') ?>";

      $.ajax({
        url: url,
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function(res) {

          if (res.status === 'success') {

            $('#msg').html(`<div class="alert alert-success">${res.message}</div>`);

            // reset form
            $('#studentForm')[0].reset();
            $('#student_id').val('');
            $('#submitBtn').text('Add').removeClass('btn-success').addClass('btn-primary');

            // reload table
            table.ajax.reload(null, false);

          } else {
            $('#msg').html(`<div class="alert alert-danger">${res.message}</div>`);
          }
        }
      });
    });



    $('#importForm').on('submit', function(e) {
      e.preventDefault();

      let formData = new FormData(this);

      $('#importLoader').show();

      $.ajax({
        url: "<?= base_url('students/import_large_ajax') ?>",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",

        success: function(res) {
          $('#importLoader').hide();

          if (res.status === 'success') {
            $('#msg').html(`<div class="alert alert-success">${res.message}</div>`);

            table.ajax.reload(null, false);

            $('#importForm')[0].reset();

          } else {
            $('#msg').html(`<div class="alert alert-danger">${res.message}</div>`);
          }
        },
        error: function() {
          $('#importLoader').hide();
          $('#msg').html(`<div class="alert alert-danger">Upload Failed</div>`);
        }
      });
    });
  </script>

</body>

</html>