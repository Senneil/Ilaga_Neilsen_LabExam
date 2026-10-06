// Lock icon toggles password visibility
document.querySelectorAll('.toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var input = btn.parentElement.querySelector('input');
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.setAttribute('aria-pressed', show);
        btn.style.opacity = show ? '.6' : '1';
    });
});
