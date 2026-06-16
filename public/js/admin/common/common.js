function togglePassword(inputId = 'password', iconId = 'togglePasswordIcon') {
    const passwordInput = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (!passwordInput || !icon) {
        return;
    }

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
