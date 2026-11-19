<div class="login-menu">
    <button type="button" class="menu-toggle" onclick="toggleMenu()">
        <i class="bi bi-list"></i>
    </button>
    <div class="menu-dropdown" id="menuDropdown">
        <a href="login_admin.php">
            <i class="bi bi-person-gear"></i>
            Login Admin
        </a>
    </div>
</div>

<div class="login-container">
    <div class="login-form">
        <div class="logo-container">
            <img src="../webabsensi/gambar/LogoDarulFatah.png" alt="LogoDarulFatah">
        </div>

        <h1>Selamat Datang</h1>

        <p class="description">
            Silakan masuk ke akun Guru Anda untuk mulai mencatat kehadiran hari ini.
        </p>

        <form method="POST" action="index.php?page=login">
            <div class="form-group">
                <label>Username</label>

                <div class="input-box">
                    <i class="bi bi-person icon-left"></i>
                    <input type="text" name="user" placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="form-group">
                <div class="password-title">
                    <label>Password</label>
                </div>

                <div class="input-box">
                    <i class="bi bi-key icon-left"></i>
                    <input type="password" name="pass" id="password" placeholder="Masukkan password" required>
                    <i class="bi bi-eye icon-right" id="togglePassword" onclick="togglePassword()"></i>
                </div>
            </div>

            <button type="submit" name="submit" class="btn-login">Masuk</button>
        </form>
    </div>
</div>

<script>

function toggleMenu() {
    const menu = document.getElementById("menuDropdown");
    menu.classList.toggle("show");
}

function togglePassword() {
    const password = document.getElementById("password");
    const icon = document.getElementById("togglePassword");

    if (password.type === "password") {
        password.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        password.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}
</script>