
$(document).ready(function () {

    $('.content').css('visibility', 'visible');
    $('.content').css('opacity', 1);
    
    $(".page-preloader").fadeOut("slow");
  
    select2 = $(".select2").select2({
        language: "es",
        theme: "bootstrap4",
        placeholder: "",
        width: '100%'
    });


});


function select_menu(menu_id) {

    var element = '#'+menu_id;

    if (!$(element).hasClass('active')) {
        $('.sidebar-menu li').removeClass('active');
        $(element).addClass('active');
    }
}

