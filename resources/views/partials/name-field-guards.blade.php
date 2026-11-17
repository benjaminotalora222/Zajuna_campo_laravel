{{-- Global Script to Prevent and Invalidate Leading Numbers & Disallowed Digits in Name Fields --}}
<script>
(function() {
    'use strict';

    // Reglas de identificación
    const ONLY_LETTERS_NAMES = [
        'name',
        'nombre_completo',
        'cliente',
        'user_name',
        'categoria'
    ];

    const NO_LEADING_NUMBER_NAMES = [
        'name',
        'nombre_completo',
        'cliente',
        'user_name',
        'categoria',
        'nombre',
        'tema',
        'titulo',
        'inpNombre',
        'inpTema',
        'inpTitulo',
        'inpEspacio'
    ];

    function isOnlyLettersField(input) {
        if (!input || input.type === 'hidden' || input.type === 'submit') return false;
        if (input.dataset && input.dataset.onlyLetters === 'true') return true;
        const name = (input.name || '').toLowerCase();
        const id   = (input.id || '').toLowerCase();
        return ONLY_LETTERS_NAMES.includes(name) ||
               id === 'inputname' ||
               id === 'inpaprendiznombrecompleto' ||
               id === 'inputcliente' ||
               id === 'inputusername' ||
               id === 'inputcategoria';
    }

    function isNoLeadingNumberField(input) {
        if (!input || input.type === 'hidden' || input.type === 'submit') return false;
        if (input.dataset && input.dataset.noLeadingNumber === 'true') return true;
        if (isOnlyLettersField(input)) return true;
        const name = (input.name || '').toLowerCase();
        const id   = (input.id || '').toLowerCase();
        return NO_LEADING_NUMBER_NAMES.includes(name) ||
               NO_LEADING_NUMBER_NAMES.includes(id) ||
               name.includes('nombre') ||
               id.includes('nombre') ||
               id.includes('titulo') ||
               id.includes('tema');
    }

    // 1. Prevenir pulsación de números si está al inicio
    document.addEventListener('keydown', function(e) {
        const input = e.target;
        if (!input || !input.tagName || input.tagName.toLowerCase() !== 'input') return;
        if (input.type && !['text', 'search'].includes(input.type)) return;

        const isOnlyLetters = isOnlyLettersField(input);
        const isNoLeading   = isNoLeadingNumberField(input);

        if (!isNoLeading && !isOnlyLetters) return;

        // Si es una tecla numérica del teclado estándar o numpad (0-9)
        const isDigit = (e.key >= '0' && e.key <= '9');

        if (isDigit) {
            // Si el campo solo admite letras, prohibir cualquier número de una vez
            if (isOnlyLetters) {
                e.preventDefault();
                input.setCustomValidity('Este campo de nombre solo debe contener letras (no se permiten números).');
                input.reportValidity();
                flashInputError(input);
                return false;
            }

            // Si es un campo de título/proyecto/proveedor, no puede comenzar con número
            if (isNoLeading && (input.selectionStart === 0 || !input.value.trim())) {
                e.preventDefault();
                input.setCustomValidity('El campo no puede comenzar con números. Ingrese una letra.');
                input.reportValidity();
                flashInputError(input);
                return false;
            }
        }
    }, true);

    // 2. Evento input: Si se pega o introduce texto que empiece con número, eliminarlo e invalidar de una vez
    document.addEventListener('input', function(e) {
        const input = e.target;
        if (!input || !input.tagName || input.tagName.toLowerCase() !== 'input') return;
        if (input.type && !['text', 'search'].includes(input.type)) return;

        const isOnlyLetters = isOnlyLettersField(input);
        const isNoLeading   = isNoLeadingNumberField(input);

        if (!isNoLeading && !isOnlyLetters) return;

        let val = input.value;

        // A. Verificar si comienza con número
        if (/^[0-9]/.test(val)) {
            input.setCustomValidity('El campo no puede comenzar con números. Se invalidó el registro.');
            input.reportValidity();
            flashInputError(input);
            // Eliminar números del inicio
            input.value = val.replace(/^[0-9]+/, '');
            val = input.value;
        }

        // B. Si es campo solo de letras, no puede contener ningún número
        if (isOnlyLetters && /[0-9]/.test(val)) {
            input.setCustomValidity('Este campo solo debe contener letras (no se permiten números).');
            input.reportValidity();
            flashInputError(input);
            // Eliminar todos los números
            input.value = val.replace(/[0-9]/g, '');
            val = input.value;
        }

        // C. Si ya está limpio y comienza con letra válida
        if (val && !/^[0-9]/.test(val)) {
            input.setCustomValidity('');
            input.classList.remove('border-red-500', 'bg-red-50');
        }
    }, true);

    // 3. Blur: limpieza y validación final
    document.addEventListener('focusout', function(e) {
        const input = e.target;
        if (!input || !input.tagName || input.tagName.toLowerCase() !== 'input') return;
        if (isNoLeadingNumberField(input)) {
            const trimmed = input.value.trim();
            if (/^[0-9]/.test(trimmed)) {
                input.setCustomValidity('El nombre no puede comenzar con números.');
                input.reportValidity();
                flashInputError(input);
            } else {
                input.setCustomValidity('');
            }
        }
    }, true);

    // 4. Interceptar Submit en cualquier formulario
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || !form.querySelectorAll) return;

        const inputs = form.querySelectorAll('input');
        for (const input of inputs) {
            if (isOnlyLettersField(input)) {
                const val = input.value.trim();
                if (/^[0-9]/.test(val) || /[0-9]/.test(val)) {
                    e.preventDefault();
                    e.stopPropagation();
                    input.setCustomValidity('El nombre no puede comenzar con números y solo debe contener letras.');
                    input.reportValidity();
                    flashInputError(input);
                    input.focus();
                    alertMessage('El campo "' + getFieldName(input) + '" no puede comenzar con números y solo debe contener letras.');
                    return false;
                }
            } else if (isNoLeadingNumberField(input)) {
                const val = input.value.trim();
                if (/^[0-9]/.test(val)) {
                    e.preventDefault();
                    e.stopPropagation();
                    input.setCustomValidity('El campo no puede comenzar con números.');
                    input.reportValidity();
                    flashInputError(input);
                    input.focus();
                    alertMessage('El campo "' + getFieldName(input) + '" no puede comenzar con números.');
                    return false;
                }
            }
        }
    }, true);

    function flashInputError(input) {
        input.classList.add('border-red-500');
        input.style.borderColor = '#ef4444';
        setTimeout(() => {
            if (!input.validationMessage) {
                input.style.borderColor = '';
                input.classList.remove('border-red-500');
            }
        }, 3000);
    }

    function getFieldName(input) {
        const label = input.closest('div')?.querySelector('label');
        if (label) {
            return label.innerText.replace(/[*]/g, '').trim();
        }
        return input.placeholder || input.name || 'Nombre';
    }

    function alertMessage(msg) {
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Error en el formulario',
                text: msg,
                confirmButtonColor: '#71277a'
            });
        }
    }
})();
</script>
