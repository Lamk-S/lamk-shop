<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectsTipoPersona = document.querySelectorAll('select[name="tipo_persona"]');

        selectsTipoPersona.forEach(select => {
            const form = select.closest('form');
            if (!form) return;

            const docSelect = form.querySelector('select[name="documento_id"]');
            const naturalFields = form.querySelectorAll('.quick-cliente-natural-field, .quick-proveedor-natural-field, .natural-field');
            const juridicaFields = form.querySelectorAll('.quick-cliente-juridica-field, .quick-proveedor-juridica-field, .juridica-field');
            
            const inputRazonSocial = form.querySelector('input[name="razon_social"]');
            const inputNombres = form.querySelector('input[name="nombres"]');
            const inputApellidos = form.querySelector('input[name="apellidos"]');

            function setRequired(elements, isRequired) {
                elements.forEach((el) => {
                    const input = el.querySelector('input, select, textarea');
                    if (input) input.required = isRequired;
                });
            }

            function autoSeleccionarDocumento(codigoBuscado) {
                if (!docSelect) return;
                const codigo = String(codigoBuscado || '').toUpperCase();
                let found = false;
                Array.from(docSelect.options).forEach((option) => {
                    if (String(option.dataset.codigo || '').toUpperCase() === codigo) {
                        option.selected = true;
                        found = true;
                    }
                });
                if (!found && docSelect.options.length > 0 && !docSelect.value) {
                    docSelect.selectedIndex = 0;
                }
            }

            function handleTipoPersonaChange() {
                const tipo = String(select.value || '').toLowerCase();
                if (tipo === 'natural') {
                    naturalFields.forEach(el => el.classList.remove('d-none'));
                    juridicaFields.forEach(el => el.classList.add('d-none'));
                    setRequired(naturalFields, true);
                    setRequired(juridicaFields, false);
                    if (inputRazonSocial) inputRazonSocial.value = '';
                    autoSeleccionarDocumento('DNI');
                } else if (tipo === 'juridica') {
                    naturalFields.forEach(el => el.classList.add('d-none'));
                    juridicaFields.forEach(el => el.classList.remove('d-none'));
                    setRequired(naturalFields, false);
                    setRequired(juridicaFields, true);
                    if (inputNombres) inputNombres.value = '';
                    if (inputApellidos) inputApellidos.value = '';
                    autoSeleccionarDocumento('RUC');
                } else {
                    naturalFields.forEach(el => el.classList.add('d-none'));
                    juridicaFields.forEach(el => el.classList.add('d-none'));
                    setRequired(naturalFields, false);
                    setRequired(juridicaFields, false);
                }
            }
            handleTipoPersonaChange();
            select.addEventListener('change', handleTipoPersonaChange);
        });
    });
</script>