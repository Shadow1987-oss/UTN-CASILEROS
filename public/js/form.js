document.addEventListener('DOMContentLoaded', function () {
    // Bloquear botón al enviar formularios con id
    function handleForm(formId) {
        var form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener('submit', function (e) {
            var btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                var original = btn.innerHTML;
                btn.innerHTML = 'Procesando...';
            }
        });
    }

    handleForm('student-form');
    handleForm('students-import-form');
    handleForm('locker-form');
    handleForm('period-form');
    handleForm('assignment-form');
    handleForm('sancion-form');
    handleForm('report-form');
    handleForm('recibo-form');
    handleForm('career-form');
    handleForm('building-form');
    handleForm('usuario-form');

    // Validación simple de email en inputs con type=email
    document.querySelectorAll('input[type="email"]').forEach(function (input) {
        input.addEventListener('input', function () {
            var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var help = input.parentElement.querySelector('.field-help');
            if (!help) {
                help = document.createElement('div');
                help.className = 'field-help';
                input.parentElement.appendChild(help);
            }
            if (input.value && !re.test(input.value)) {
                help.textContent = 'Formato de correo inválido.';
                help.classList.add('error');
            } else {
                help.textContent = '';
                help.classList.remove('error');
            }
        });
    });
});
