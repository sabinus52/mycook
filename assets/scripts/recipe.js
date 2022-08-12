/**
 * Pour la gestion du formulaire des recettes
 */

import 'typeahead.js';
import Bloodhound from 'bloodhound-js';

import './collection.js';
require('select2');

var olix = require('./olix.js');

/**
 * Rafraichit et initialise les éléments de l'auto complétion des recettes
 */
var refreshElementIngredientAutoComplete = function() {

    var ingredients = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: routefetchJsonIngredient,
            wildcard: 'QQUERY'
          }
    });

    // Initialisation de l'auto complétion
    $('input.autocomplete').typeahead('destroy');
    $('input.autocomplete').typeahead({
        hint: true,
        highlight: true,
        minLength: 1
    },
    {
        name: 'ingredients',
        display: 'name',
        source: ingredients
    });

    // Suite à la saisie de l'ingrédient, on vérifie qu'il existe sinon on demande sa création
    $('input.autocomplete').on('focusout', function(_ev) {

        // Si vide alors on ne fait rien
        console.log('Selection : ' + $(this).val());
        if ($(this).val() == '') return;

        //Test si l'ingrédient existe
        $.getJSON(routeIsExistIngredient, { term: $(this).val() })
            .done(function( json ) {
                if ( json.id == 0 ) {
                    // L'ingrédient n'existe pas alors on affiche le modal
                    $('#confirmCreateIngredient .modal-body').removeClass('text-center').html($('#confirmCreateIngredient .modal-body').data('prototype'));
                    $('#confirmCreateIngredient .modal-footer').show();
                    $('#formCreateIngredient input.ingredient').val(json.name);
                    $('#newIngredient').text(json.name);
                    $('#confirmCreateIngredient').modal('show');
                } else {
                    // L'ingrédient existe alors on remplit automatiquement l'unité
                    var obj = $(_ev.target).closest('div.form-row').find('.unity').first();
                    obj.val(json.unity);
                }
            });
    });
};


$(function() {

    // Initialise la liste des catégories
    $('#recipe_category').select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
        closeOnSelect: !$(this).attr('multiple'),
    });

    // Initialise la collection des ingrédients et des étapes
    $('.collection-widget').olixCollection({
        onAddItem: function ($prototype, $inputFisrt) {
            refreshElementIngredientAutoComplete();
            $inputFisrt.focus();
        }
    });

    // Initialise l'auto complétion des ingrédients
    refreshElementIngredientAutoComplete();

    // Ouvre le modal pour demander si on doit créer un ingrédient
    $('#confirmCreateIngredient').on('click', 'button.btn-primary',  function (_ev) {
        olix.submitFormModalAjax('#confirmCreateIngredient', '#formCreateIngredient', routeCreateIngredient);
    });

});