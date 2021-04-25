
const $ = require('jquery');
window.$ = window.jQuery = $;

import 'typeahead.js';
import Bloodhound from 'bloodhound-js'


/**
* Soumission d'un formulaire en Ajax depuis une boite de dialogue
* 
* @param string modal : Nom du modal #modal
* @param string form  : Nom du formulaire #form
* @param string path  : Chemin du script php
*/
function submitFormModalAjax (modal, form, path)
{
   $.ajax({
       type: "POST",
       url: path,
       data: $(form).serialize(),
       cache: false,
       beforeSend: function() {
           $(modal+' .modal-body').addClass('text-center').html('<p><img alt="" src="/images/spinner-rectangle.gif"></p>');
           $(modal+' .modal-footer').html('');
       },
       success: function(data) {
           if (data == 'OK') {
               $(modal).modal('hide');
           } else {
               $(modal+' .modal-dialog').html(data);
           }
       },
       error: function () {
           alert("Une erreur est survenue lors de validation du formulaire.");
       }
   });
};

function refreshElement() {

    var states = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        // `states` is an array of state names defined in "The Basics"
        //prefetch: '../../ingredient/list.json',
        //prefetch: 'https://raw.githubusercontent.com/twitter/typeahead.js/gh-pages/data/countries.json',
        remote: {
            url: routefetchJsonIngredient,
            wildcard: 'QQUERY'
          }
        //local: states
    });

    $('input.autocomplete').typeahead('destroy');
    $('input.autocomplete').typeahead({
        hint: true,
        highlight: true,
        minLength: 1
    },
    {
        name: 'states',
        display: 'name',
        source: states
    });

    $('input.autocomplete').on('focusout', function(_ev) {
        console.log('Selection : ' + $(this).val());
        if ($(this).val() == '') return;
        $.getJSON(routeIsExistIngredient, { term: $(this).val() })
            .done(function( json ) {
                console.log( "JSON Data: " + json.id +' - '+ json.name );
                if ( json.id == 0 ) {
                    //alert("Cet ingrédient n'existe pas");
                    $('#formCreateIngredient input.ingredient').val(json.name);
                    $('#newIngredient').text(json.name);
                    $('#confirmCreateIngredient').modal('show');
                }
            });
    });
    
};

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

    $('.collection-widget').olixCollection({
        onAddItem: function () {
            refreshElement();
        }
    });

    refreshElement();


    $('#confirmCreateIngredient').on('click', 'button.btn-primary',  function (_ev) {
        submitFormModalAjax('#confirmCreateIngredient', '#formCreateIngredient', routeCreateIngredient);
    })
      

});
