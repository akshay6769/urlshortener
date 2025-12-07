<?php
// src/views/invitation/send.php
ob_start();
?>
<h2>Send Invitation</h2>
<form method="post" action="/invite/send">
  <label>Email</label>
  <input type="email" name="email" required />
  <label>Role</label>
  <select name="role">
    <option value="member">Member</option>
    <option value="admin">Admin</option>
  </select>
  <?php if (current_user()['role'] === 'superadmin'): ?>
    <label>Company ID</label>
    <input type="text" name="company_id" placeholder="company id (for superadmin)" />
    <div class="small muted">SuperAdmin: enter company id to invite for that company.</div>
  <?php endif; ?>
  <button class="btn" type="submit">Send Invitation</button>
</form>
<?php
$content = ob_get_clean();
require __DIR__.'/../layout.php';
