document.addEventListener('DOMContentLoaded', function () {

    // 1. ACTIVAR/DESACTIVAR LA NAVEGACIÓN LATERAL
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }
    
    // 2. LÓGICA DEL MODAL DE CONFIRMACIÓN GLOBAL (Movido del app.blade.php)
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
                } else {
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

    // 3. ATAJOS DE TECLADO (Hotkeys)
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

        if (e.key === 'F2') {
            e.preventDefault();
            window.location.href = "/ventas/create";
        }
        if (e.key === 'F3') {
            e.preventDefault();
            window.location.href = "/kardex";
        }
        // F4: Ver Arqueo de Caja actual
        if (e.key === 'F4') {
            e.preventDefault();
            window.location.href = "/sesiones-caja";
        }
    });

    // 4. BLOQUEO DE DOBLE SUBMIT EN FORMULARIOS
    document.addEventListener('submit', function(e) {
        if (e.target.tagName === 'FORM' && !e.target.classList.contains('no-disable')) {
            const submitBtn = e.target.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                const originalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cargando...';
                
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }, 7000);
            }
        }
    });

    // 5. BÚSQUEDA GLOBAL DE DNI/RUC (APIS PERÚ) PARA CUALQUIER MODAL
    $('#btnBuscarDoc, #btnBuscarDocProv').on('click', function() {
        const $btn = $(this);
        const originalHtml = $btn.html();
        const documento = $('#modal_numero_documento').val().trim();

        if (documento.length !== 8 && documento.length !== 11) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: 'Ingrese DNI (8) o RUC (11).', showConfirmButton: false, timer: 2000 });
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/api-peru/consultar',
            method: 'GET',
            data: { documento: documento },
            success: function(res) {
                if (documento.length === 8) {
                    $('#modal_nombres').val(res.nombres);
                    $('#modal_apellidos').val(res.apellidoPaterno + ' ' + res.apellidoMaterno);
                    $('#modal_tipo_persona').val('natural').trigger('change');
                } else {
                    $('#modal_razon_social').val(res.razonSocial);
                    $('#modal_direccion').val(res.direccion || '');
                    $('#modal_tipo_persona').val('juridica').trigger('change');
                }
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Datos recuperados.', showConfirmButton: false, timer: 2000 });
            },
            error: function(xhr) {
                let msg = 'Error al buscar el documento.';
                if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: msg, showConfirmButton: false, timer: 2000 });
                $('#modal_nombres, #modal_apellidos, #modal_razon_social, #modal_direccion').val('');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});