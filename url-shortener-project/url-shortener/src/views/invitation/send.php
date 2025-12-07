<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-7">

        <div class="card card-dark shadow">

            <div class="card-header fw-bold">
                Send Invitation
            </div>

            <div class="card-body">

                <form method="POST" action="/invite/send">

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               required>
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <!-- Company Name (SuperAdmin only) -->
                    <?php if (current_user()['role'] === 'superadmin'): ?>
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>

                            <input type="text"
                                   name="company_name"
                                   class="form-control"
                                   required
                                   placeholder="e.g., ASP Corp., Sembark Tech">

                            <div class="form-text text-secondary">
                                Enter the new client company name. It will be created automatically.
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Buttons -->
                    <button type="submit" class="btn btn-primary">
                        Send Invitation
                    </button>

                    <a href="/dashboard" class="btn btn-outline-light ms-2">
                        Back
                    </a>

                </form>

            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
