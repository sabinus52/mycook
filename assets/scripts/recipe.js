/**
 * Script concernant la page Recipe
 */
import "select2/dist/css/select2.min.css";
import $ from "jquery";
import "olix-backoffice/scripts/select2.js";
import "olix-backoffice/scripts/collection.js";

export default {
    init: function () {
        // Initialisation des widgets Select2
        $("[data-toggle='select2']").OlixSelect2();
        /*$(document).on("select2:open", () => {
            document
                .querySelector(
                    ".select2-container--open .select2-search__field"
                )
                .focus();
        });*/

        // Initialisation de la collection des ingrédients
        $("#recipe_ingredients").OlixCollection({
            onAddItem: function ($prototype, $inputFirst) {
                console.log("recipe.init.onAddItem");
                $inputFirst.OlixSelect2({
                    tags: true,
                });
                $inputFirst.focus();
                $inputFirst.select2("open");

                // Lorsque l'utilisateur choisi un ingrédient dans la liste
                $inputFirst.on("select2:close", function (_ev) {
                    // Objet de l'option sélectionnée
                    var $selectedOption = $(_ev.target).find(":selected");
                    // Si l'ingrédient existe alors on remplit automatiquement l'unité
                    if ($selectedOption.data("unity")) {
                        // Objet de la liste des unités qui sera remplie en fonction de l'ingrédient sélectionné
                        var $unitySelectForm = $(_ev.target)
                            .closest("div.form-row")
                            .find(".unity")
                            .first();
                        // Remplissage de la liste des unités
                        $unitySelectForm.val($selectedOption.data("unity"));
                    } else {
                        // Sinon on crée un nouvel ingrédient
                        $selectedOption = $(_ev.target);
                        console.log("ingredient not exist", $selectedOption);
                        /*$("#modalOlix .modal-body").load(
                            $selectedOption.data("route")
                        );*/
                        $("#modalOlix").modal("show");
                    }
                });
            },
        });

        // Lorsque l'utilisateur choisi un ingrédient dans la liste
        /*$("#recipe_ingredients .ingredient").on("change", function (_ev) {
            // Objet de l'option sélectionnée
            const $selectedOption = $(_ev.target).find(":selected");
            // Objet de la liste des unités qui sera remplie en fonction de l'ingrédient sélectionné
            var $unitySelectForm = $(_ev.target)
                .closest("div.form-row")
                .find(".unity")
                .first();
            // Remplissage de la liste des unités
            $unitySelectForm.val($selectedOption.data("unity"));
        });*/

        // Initialisation de la collection des étapes
        $("#recipe_steps").OlixCollection();
    },
};
