
    function createDataTable(sortcolumns, route, search) {

       var dataTable = $('#dataTable').DataTable({
            autoWidth: false,
            oSearch: {"sSearch": search},

            order: [[0, "DESC"]], // Define el orden por defecto y la columna a ordenar
            columnDefs: [
                { orderable: false, targets: sortcolumns}, //Define las columnas que  no van a ser ordenables
		  { searchable: false, targets: 0 }
            ],
            deferRender: true, //Opcion que permite una mayor velocidad de inicializacion(disponible desde la version 1.10)
            language: {
                paginate: {
                    first: "<<",
                    previous: "<",
                    next: ">",
                    last: ">>"
                },
                sLengthMenu: "Mostrar _MENU_ registros",
                sInfo: "Mostrando _START_ al _END_ de _TOTAL_ elementos",
                sInfoEmpty: "No hay datos para mostrar",
                sEmptyTable: "No hay datos para mostrar",
                sInfoFiltered: "(filtrado de _MAX_ elementos en total)",
                sLoadingRecords: "Cargando...",
                sProcessing: '<span class="spinner-border text-primary "></span>',
                sSearch: "Buscar:",
                sZeroRecords: "No se encontraron resultados"
            },
            processing: true,
            serverSide: true,
            ajax: route
        });

       return dataTable;

    }

    function filterColumn ( i ) {
        if(i) {
            value = $('#col' + i + '_filter').val()
            $('#dataTable').DataTable().column(i).search(value);
        }
    }

