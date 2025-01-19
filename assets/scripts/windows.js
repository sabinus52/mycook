/**
 * Action à la fin de chargement de la page
 */
import $ from "jquery";

var browserWindow = $(window);

/**
 * Chargement de la page
 */
export function preLoadWindow() {
    console.log("preLoadWindow");
    $("#preloader").fadeOut("slow", function () {
        $(this).remove();
    });
}
