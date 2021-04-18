
const $ = require('jquery');
window.$ = window.jQuery = $;

$(function() {

    // Cache les alertes
    setTimeout(function() {
        if ($('.alert').hasClass('alert-dismissable') )
            $('.alert').slideUp('slow');
    }, 5000);


    // Initialise la liste des catégories
    $('#recipe_category').select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
        closeOnSelect: !$(this).attr('multiple'),
    });

    $('.collection-widget').olixCollection();

});


