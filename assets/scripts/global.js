
const $ = require('jquery');
window.$ = window.jQuery = $;

import 'typeahead.js';
import Bloodhound from 'bloodhound-js';


$(function() {

    // Cache les alertes
    setTimeout(function() {
        if ($('.alert').hasClass('alert-dismissable') )
            $('.alert').slideUp('slow');
    }, 5000);

    // Filtre des ingrédients
    $('.filterIngredient').change(function (_ev) {
        location.href = this.value;
    });

});
