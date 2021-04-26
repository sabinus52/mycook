/**
 * Utilitaires
 * 
 * @author Olivier <sabinus52@gmail.com>
 * 
 * @file olix.js
 * @package Olix
 */

module.exports = olix;

var olix = {

    /**
     * Soumission d'un formulaire en Ajax depuis une boite de dialogue
     * 
     * @param string modal : Nom du modal #modal
     * @param string form  : Nom du formulaire #form
     * @param string path  : Chemin du script php
     */
    submitFormModalAjax: function (modal, form, path)
    {
        $.ajax({
            type: "POST",
            url: path,
            data: $(form).serialize(),
            cache: false,
            beforeSend: function() {
                $(modal + ' .modal-body').addClass('text-center').html('<p><img alt="" src="/images/spinner-rectangle.gif"></p>');
                $(modal + ' .modal-footer').html('');
            },
            success: function(data) {
                if (data == 'OK') {
                    $(modal).modal('hide');
                } else {
                    $(modal + ' .modal-dialog').html(data);
                }
            },
            error: function () {
                alert("Une erreur est survenue lors de validation du formulaire.");
            }
        });
    },

};

