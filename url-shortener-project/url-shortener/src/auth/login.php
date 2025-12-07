<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-5">

        <div class="card card-dark shadow">

            <div class="card-body">

                <h3 class="card-title mb-4 text-center">Login</h3>

                <form method="POST" action="/login">

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            required
                        >
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required
                        >
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary w-100">
                        Login
                    </button>

                </form>

                <p class="text-secondary small mt-3 mb-0">
                    Login using the SuperAdmin created via seeder, or using an invited account.
                </p>

            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../views/layout.php';
?>
