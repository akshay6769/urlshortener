<?php ob_start(); ?>

<h2 class="mb-4">Create Your Account</h2>

<form method="POST" action="/invite/accept">

    <!-- Security Token -->
    <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">

    <!-- Invited Email (Read Only) -->
    <div class="mb-3">
        <label class="form-label">Organization Email</label>
        <input type="email"
               class="form-control"
               value="<?= htmlspecialchars($inv['email']) ?>"
               readonly>
    </div>

    <!-- Assigned Role (Read Only) -->
    <div class="mb-3">
        <label class="form-label">Assigned Role</label>
        <input type="text"
               class="form-control"
               value="<?= htmlspecialchars($inv['role']) ?>"
               readonly>
    </div>

    <!-- Password Creation -->
    <div class="mb-3">
        <label class="form-label">Create Password</label>
        <input type="password"
               name="password"
               required
               class="form-control">
    </div>

    <!-- Submit -->
    <button class="btn btn-primary w-100">
        Create Account
    </button>

</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
