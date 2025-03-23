/**
 * Action à la fin de chargement de la page
 */
import $ from "jquery";
import WOW from "wow.js";
import Modal from "olix-backoffice/plugins/modal.js";

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

/**
 * Initialise la page
 */
export function initializeWindow() {
    console.log("loadWindow");

    // ScrollUp
    if ($.fn.scrollUp) {
        browserWindow.scrollUp({
            scrollSpeed: 1500,
            scrollText: '<i class="fa fa-angle-up"></i>',
        });
    }

    // Search Wrapper
    var searchWrapper = $(".search-wrapper");
    $(".search-btn").on("click", function () {
        searchWrapper.toggleClass("on");
    });
    $(".close-btn").on("click", function () {
        searchWrapper.removeClass("on");
    });

    // Classy Nav
    if ($.fn.classyNav) {
        $("#deliciousNav").classyNav();
    }

    // Modal
    Modal.initialize();

    // Effets sur les éléments au moment de leur affichage en scrollant
    if (browserWindow.width() > 767) {
        new WOW().init();
    }

    // Toast
    $(".toast").toast("show");

    // Renvoi vers la recette si on clique sur le bouton de recherche
    $("select[name='olix-search'").on("change", function (e) {
        if (this.value) {
            location.href = this.value;
        }
        $(this).prop("selectedIndex", 0);
    });
}
