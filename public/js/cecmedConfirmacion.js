
function createDelete(rutaEliminar){

    Swal.fire({
            title: "CONFIRMACIÓN",
            text: "Esta acción no se podrá deshacer. Seguro que desea eliminar este elemento?",
            type: "error",
            showCancelButton: true,
            confirmButtonClass: "btn btn-danger",
            cancelButtonClass: "btn btn-light",
            cancelButtonText: "No, cancelar!",
            confirmButtonText: "Si, eliminar!",
            showLoaderOnConfirm: true,

            preConfirm: function () {
                $.ajax({
                    url: rutaEliminar,
                    method: "POST"
                }).always(function () {
                    swal.close();
                    location.reload(true);
                });
            }

    });

}