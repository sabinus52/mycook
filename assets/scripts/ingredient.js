/**
 * Script concernant la page Ingredient
 */
import $ from "jquery";

var Ingredient = {
    /**
     * Initialisation de la page
     */
    init: function () {
        console.log("Ingredient.init");
        // Filtre des ingrédients
        $("#filterIngredient").on("change", function (_ev) {
            location.href = this.value;
        });
    },
};

export default Ingredient;
