
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
    $('.filterIngredient').on('change', function (_ev) {
        location.href = this.value;
    });

    // Tooltip
    $("[data-toggle='tooltip']").tooltip();

    // Recherche des recettes par autocompletion
    var engine = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: Routing.generate('autocomplete-search', { term: '%' }),
        remote: {
            url: Routing.generate('autocomplete-search', { term: 'TERM' }),
            wildcard: 'TERM'
        }
    });

    $('#sdSearch').typeahead(null, {
        name: 'myautocomplete',
        display: 'name',
        source: engine
    }).on('typeahead:select', function(ev, suggestion) {
        location.href = Routing.generate('recipe_show', { id: suggestion.id })
    });;

});
