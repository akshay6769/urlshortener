<?php
// src/views/short/create.php
ob_start();
?>
<div class="row justify-content-center">
  <div class="col-md-7 col-lg-6">
    <div class="card card-dark shadow">
      <div class="card-header fw-bold">Generate Short URL</div>
      <div class="card-body">
        <?php if (!empty($err)): ?>
          <div class="alert alert-danger"><?= $err ?></div>
        <?php endif; ?>

        <form method="post" action="/short/store">
          <div class="mb-3">
            <label class="form-label">Long URL</label>
            <input type="text" name="target_url" class="form-control" required placeholder="https://example.com/long/path" />
          </div>
          <button class="btn btn-primary" type="submit">Generate</button>
          <a href="/dashboard" class="btn btn-outline-light ms-2">Back</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__.'/../layout.php';
