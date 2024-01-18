
$(document).ready(function () {


    $(".form").validate({

        errorPlacement: function (error, element) {
            var elem = $(element).parent().find('span.error');

            if(elem.length == 0){
                elem = $(element).parent().parent().find('span.error');
            }

            error.insertAfter(elem);
        },

        highlight: function (element) { // hightlight error inputs
            $(element)
                .closest('.form-group').addClass('text-danger'); // set error class to the control group
        },

        unhighlight: function (element) { // revert the change done by hightlight
            $(element)
                .closest('.form-group').removeClass('text-danger'); // set error class to the control group
        },

    });

});


