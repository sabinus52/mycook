
const $ = require('jquery');
window.$ = window.jQuery = $;

$(function() {

    // Cache les alertes
setTimeout(function() {
    if ($('.alert').hasClass('alert-dismissable') )
        $('.alert').slideUp('slow');
}, 5000);

});


