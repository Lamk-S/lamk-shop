<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistema de Punto de Venta Corporativo" />
    <meta name="author" content="Lamk-S" />
    <title>Lamk Sports | @yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous" defer></script>

    @stack('css-datatable')
    @stack('css')
    @vite(['resources/js/app.js'])

    <style>
        body { background-color: #f4f6f9; color: #333; }
        .sb-nav-fixed #layoutSidenav #layoutSidenav_content { background-color: #f8f9fa; }
    </style>
</head>
<body class="sb-nav-fixed">
    <x-navigation-header />

    <div id="layoutSidenav">
        <x-navigation-menu />

        <div id="layoutSidenav_content">
            <main>
                @yield('content')
            </main>
            <x-footer />
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(function () {
            if($.fn.selectpicker) {
                $('.selectpicker').selectpicker();
            }
        });
    </script>
    
    <script src="{{ asset('js/scripts.js') }}"></script>

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
                        if (input) {
                            input.required = isRequired;
                        }
                    });
                }

                function autoSeleccionarDocumento(codigoBuscado) {
                    if (!docSelect) return;
                    const codigo = String(codigoBuscado || '').toUpperCase();
                    let found = false;

                    Array.from(docSelect.options).forEach((option) => {
                        const optionCodigo = String(option.dataset.codigo || '').toUpperCase();
                        if (optionCodigo === codigo) {
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

    @include('layouts.partials.alert')

    <x-modal-confirmacion />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalConfirmObj = document.getElementById('modalConfirmacionGlobal');
            
            if (modalConfirmObj) {
                const modalConfirm = new bootstrap.Modal(modalConfirmObj);
                
                document.body.addEventListener('click', function(e) {
                    const btn = e.target.closest('.btn-confirmar');
                    
                    if (btn) {
                        e.preventDefault();
                        
                        const { nombre, accion, url, metodo } = btn.dataset;
                        
                        document.getElementById('formConfirmacionGlobal').action = url;
                        document.getElementById('formMethodInput').value = metodo; 
                        
                        const iconContainer = document.getElementById('modalIconContainer');
                        const title = document.getElementById('modalTitle');
                        const desc = document.getElementById('modalDesc');
                        const btnSubmit = document.getElementById('modalBtnConfirm');

                        if (accion === 'desactivar' || accion === 'bloquear' || accion === 'suspender') {
                            iconContainer.className = 'bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-2';
                            iconContainer.innerHTML = '<i class="fas fa-trash-alt fa-3x"></i>'; 
                            
                            title.textContent = '¿Eliminar / Desactivar?';
                            desc.innerHTML = `El registro de <strong>${nombre}</strong> pasará a la papelera.`;
                            btnSubmit.className = 'btn btn-danger fw-bold px-4 rounded-pill shadow-sm';
                        }
                        else {
                            iconContainer.className = 'bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2';
                            iconContainer.innerHTML = '<i class="fas fa-trash-restore-alt fa-3x"></i>'; 
                            
                            title.textContent = '¿Restaurar registro?';
                            desc.innerHTML = `El registro de <strong>${nombre}</strong> volverá a estar operativo.`;
                            btnSubmit.className = 'btn btn-success fw-bold px-4 rounded-pill shadow-sm';
                        }
                        
                        modalConfirm.show();
                    }
                });
            }
        });
    </script>

    @stack('js')
</body>
</html>