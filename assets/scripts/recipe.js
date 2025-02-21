/**
 * Script concernant la page Recipe
 */
import "select2/dist/css/select2.min.css";
import $ from "jquery";
import "olix-backoffice/plugins/select2.js";
import "olix-backoffice/plugins/collection.js";

export default {
    init: function () {
        // Initialisation de Select2
        $("[data-toggle='select2']").OlixSelect2();

        // Initialisation de la collection des ingrédients
        $("#recipe_ingredients").OlixCollection({
            onAddItem: function ($prototype, $inputFirst) {
                console.log("recipe.init.onAddItem");
                // Initialisation de Select2
                $inputFirst.OlixSelect2({
                    tags: true,
                    templateSelection: function (item) {
                        if (!item.unity) return item.text;

                        // Crée un élément option avec un attribut `data-unity`
                        let $option = $("<span>")
                            .text(item.text)
                            .attr("data-unity", item.unity);

                        return $option;
                    },
                });
                $inputFirst.focus();
                $inputFirst.select2("open");

                // Lorsque l'utilisateur choisi un ingrédient dans la liste
                $inputFirst.on("select2:select", function (_ev) {
                    // Objet de l'option sélectionnée
                    var $selectedOption = $(_ev.target).find(":selected");
                    if ($selectedOption.data("select2-tag")) {
                        // Si l'ingrédient est un tag alors on crée un nouvel ingrédient au moment de l'ajout
                        console.log(
                            "New ingredient : ",
                            $selectedOption.text()
                        );
                    }
                    // Si l'ingrédient existe alors on remplit automatiquement l'unité
                    let selectedData = _ev.params.data;
                    console.log("selectedData", selectedData);
                    if (selectedData.unity) {
                        // Objet de la liste des unités qui sera remplie en fonction de l'ingrédient sélectionné
                        var $unitySelectForm = $(_ev.target)
                            .closest("div.form-row")
                            .find(".unity")
                            .first();
                        // Remplissage de la liste des unités
                        $unitySelectForm.val(selectedData.unity);
                    }
                });
            },
        });

        // Initialisation de la collection des étapes
        $("#recipe_steps").OlixCollection();
    },
};
