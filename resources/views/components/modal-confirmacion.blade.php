<div class="modal fade" id="modalConfirmacionGlobal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center p-4 pb-5">
                
                <!-- JS inyectará el icono aquí -->
                <div id="modalIconContainer" class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px;">
                </div>
                
                <h4 class="fw-bold text-dark" id="modalTitle">¿Confirmar acción?</h4>
                <p class="text-muted mb-4" id="modalDesc"></p>
                
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                    
                    <form id="formConfirmacionGlobal" action="" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="formMethodInput" value="DELETE">
                        <button type="submit" class="btn fw-bold px-4 rounded-pill shadow-sm" id="modalBtnConfirm">
                            Confirmar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>