<?php
/**
 * Controleur de la gestion des recettes
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Service\RecipeUploader;
use App\Entity\Ingredient;
use App\Form\IngredientHiddenType;
use App\Entity\RecipeIngredient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recipe")
 */
class RecipeController extends AbstractController
{

    /**
     * Index ou liste des recettes
     * 
     * @Route("/", name="recipe_index", methods={"GET"})
     */
    public function index(RecipeRepository $recipeRepository): Response
    {
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipeRepository->findAll(),
        ]);
    }


    /**
     * Création d'une recette
     * 
     * @Route("/create", name="recipe_create", methods={"GET","POST"})
     */
    public function create(Request $request, RecipeUploader $fileUploader): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recipe);
            $entityManager->flush();

            $image = $form->get('image')->getData();
            if ($image) {
                $fileUploader->upload($image, $recipe);
            }

            $this->setPopularityUnityToIngredient();

            return $this->redirectToRoute('recipe_index');
        }

        $formIngredient = $this->createForm(IngredientHiddenType::class, new Ingredient());

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'formingredient' => $formIngredient->createView(),
        ]);
    }


    /**
     * Visualisation d'une recette
     * 
     * @Route("/{id}", name="recipe_show", methods={"GET"})
     */
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }


    /**
     * Edition d'une recette
     * 
     * @Route("/{id}/update", name="recipe_update", methods={"GET","POST"})
     */
    public function update(Request $request, Recipe $recipe, RecipeUploader $fileUploader): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            if ($image) {
                $fileUploader->upload($image, $recipe);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->setPopularityUnityToIngredient();

            return $this->redirectToRoute('recipe_index');
        }

        // Formulaire pour la création d'un nouvel ingrédient
        $formIngredient = $this->createForm(IngredientHiddenType::class, new Ingredient());

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'formingredient' => $formIngredient->createView(),
        ]);
    }


    /**
     * Suppression d'une recette
     * 
     * @Route("/{id}", name="recipe_delete", methods={"POST"})
     */
    public function delete(Request $request, Recipe $recipe): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('recipe_index');
    }


    /**
     * Mets à jour les unités les plus utilisées par ingrédient
     */
    private function setPopularityUnityToIngredient()
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Récupération des tous les ingrédients
        $ingredients = $this->getDoctrine()
            ->getRepository(Ingredient::class)
            ->findAll();

        // Récupération par ingrédient l'unité la plus utilisée
        $ingredientsByUnity = $this->getDoctrine()
            ->getRepository(RecipeIngredient::class)
            ->findMostPopularityUnityByIngredient();
        
        foreach ($ingredients as $ingredient) {
            // Pas encore d'ingrédient utilisé
            if ( ! isset($ingredientsByUnity[$ingredient->getId()]) ) continue;

            $unity = $ingredientsByUnity[$ingredient->getId()];
            // Si changement d'unité, on met à jour l'unité la plus utilisée
            if ( $unity->getValue() != $ingredient->getUnity()->getValue() ) {
                $ingredient->setUnity($unity);
                $entityManager->persist($ingredient);
            }
        }
        $entityManager->flush();
    }

}