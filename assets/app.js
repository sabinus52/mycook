/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
//import "./styles/style.css";
import "bootstrap/dist/css/bootstrap.min.css";
import "./styles/app.scss";
import "./styles/classy-nav.min.css";
import "@fortawesome/fontawesome-free/css/all.min.css";
import "bootstrap";
import "./plugins/scrollUp.js";
import "./plugins/classyNav.js";
import "olix-backoffice/scripts/modal.js";

// Attente de la fin de chargement de la page
import { preLoadWindow, initializeWindow } from "./scripts/windows.js";
window.addEventListener("load", preLoadWindow);
initializeWindow();
import Ingredient from "./scripts/ingredient.js";
Ingredient.init();

import Recipe from "./scripts/recipe.js";
Recipe.init();

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");
