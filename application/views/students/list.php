<!DOCTYPE html>
<html>

<head>
  <title>School App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-4">
    <h3 class="mb-4">School Management</h3>

    <div class="card">
      <div class="card-body">

        <form method="post" action="<?= base_url('students/store') ?>" class="row g-2 mb-3">
          <div class="col">
            <input type="text" name="name" class="form-control" placeholder="Name" required>
          </div>
          <div class="col">
            <input type="email" name="email" class="form-control" placeholder="Email">
          </div>
          <div class="col">
            <input type="text" name="phone" class="form-control" placeholder="Phone">
          </div>
          <div class="col">
            <button class="btn btn-primary">Add</button>
          </div>
        </form>

        <form method="post" action="<?= base_url('students/import') ?>" enctype="multipart/form-data" class="mb-3">
          <input type="file" name="file" required>
          <button class="btn btn-success btn-sm">Import Excel</button>
          <a href="<?= base_url('students/export') ?>" class="btn btn-warning btn-sm">Export Excel</a>
        </form>

        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $s): ?>
              <tr>
                <td><?= $s->name ?></td>
                <td><?= $s->email ?></td>
                <td><?= $s->phone ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</body>

</html>