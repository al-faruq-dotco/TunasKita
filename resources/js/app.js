document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);

        if (!input) {
            return;
        }

        const shouldShow = input.type === 'password';
        input.type = shouldShow ? 'text' : 'password';
        button.textContent = shouldShow ? 'Sembunyikan' : 'Tampilkan';
        button.setAttribute('aria-pressed', String(shouldShow));
    });
});
