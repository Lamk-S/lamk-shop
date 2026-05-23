@if(session('success') || session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            customClass: {
                popup: 'shadow-sm border-0 rounded-3'
            },
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        const successMsg = "{{ session('success') }}";
        const errorMsg = "{{ session('error') }}";

        if (successMsg !== "") {
            Toast.fire({ icon: 'success', title: successMsg });
        }
        
        if (errorMsg !== "") {
            Toast.fire({ icon: 'error', title: errorMsg });
        }
    });
</script>
@endif