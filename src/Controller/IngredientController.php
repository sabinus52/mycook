<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Form\IngredientHiddenType;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur de la gestion des ingrédients.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @Route("/ingredient")
 */
class IngredientController extends AbstractController
{
    /**
     * Index ou liste des ingrédients.
     *
     * @Route("/", name="ingredient_index", methods={"GET"})
     */
    public function index(IngredientRepository $ingredientRepository, SessionInterface $session, Request $request): Response
    {
        $session->set('ingredient.filter.route', $request->get('_route'));

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredientRepository->findAll(),
        ]);
    }

    /**
     * Index ou liste des ingrédients avec le champs "calorie" non rempli.
     *
     * @Route("/without-calories", name="ingredient_index_without_cal", methods={"GET"})
     */
    public function indexWithoutCalories(IngredientRepository $ingredientRepository, SessionInterface $session, Request $request): Response
    {
        $session->set('ingredient.filter.route', $request->get('_route'));

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredientRepository->findByCalorie(null),
        ]);
    }

    /**
     * Index ou liste des ingrédients.
     *
     * @Route("/{term}.json", name="ingredient_json")
     */
    public function fetchFormatJSON(Request $request, IngredientRepository $ingredientRepository): JsonResponse
    {
        return new JsonResponse($ingredientRepository->searchByName($request->get('term'), Query::HYDRATE_ARRAY));
    }

    /**
     * Retourne si un ingredient existe ou pas.
     *
     * @Route("/is-exists", name="ingredient_isexists")
     */
    public function isExists(Request $request, IngredientRepository $ingredientRepository): JsonResponse
    {
        $ingredient = $ingredientRepository->findOneByName($request->get('term'));

        if (!$ingredient) {
            return new JsonResponse(['id' => 0, 'name' => $request->get('term'), 'unity' => '']);
        }

        return new JsonResponse([
            'id' => $ingredient->getId(),
            'name' => $ingredient->getName(),
            'unity' => $ingredient->getUnity()->getValue(),
        ]);
    }

    /**
     * Création d'un nouvel ingrédient depuis le formulaire de la recette.
     *
     * @Route("/create-ajax", name="ingredient_create_from_recipe", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function createFromRecipe(Request $request): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientHiddenType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ingredient);
            $entityManager->flush();

            return new Response('OK');
        }

        return new Response('ERROR');
    }

    /**
     * Création d'un nouvel ingrédient.
     *
     * @Route("/create", name="ingredient_create", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request, SessionInterface $session): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ingredient);
            $entityManager->flush();

            $this->updateRecipeCalories($ingredient);

            $this->get('session')->getFlashBag()->add('success', "L'ingrédient <strong>".$ingredient->getName().'</strong> a été ajouté avec succès');

            return $this->redirectToRoute($session);
        }

        return $this->render('ingredient/edit.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Visualisation d'un ingrédient.
     *
     * @Route("/{id}", name="ingredient_show", methods={"GET"})
     */
    public function show(Ingredient $ingredient): Response
    {
        return $this->render('ingredient/show.html.twig', [
            'ingredient' => $ingredient,
        ]);
    }

    /**
     * Edition d'un ingrédient.
     *
     * @Route("/{id}/update", name="ingredient_update", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(Request $request, Ingredient $ingredient, SessionInterface $session): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->updateRecipeCalories($ingredient);

            $this->get('session')->getFlashBag()->add('success', "L'ingrédient <strong>".$ingredient->getName().'</strong> a été modifié avec succès');

            return $this->redirectToRoute($session);
        }

        return $this->render('ingredient/edit.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Suppression d'un ingrédient.
     *
     * @Route("/{id}/delete", name="ingredient_delete", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Ingredient $ingredient): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ingredient->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ingredient);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', "L'ingrédient <strong>".$ingredient->getName().'</strong> a été supprimé avec succès');
        }

        return $this->redirectToRoute('ingredient_index');
    }

    /**
     * Mise à jour des calories suite à une mise à jour d'un ingrédient.
     */
    private function updateRecipeCalories(Ingredient $ingredient): void
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Récupération des recettes avec l'ingrédient
        $recipesIngredients = $entityManager->getRepository(RecipeIngredient::class)->findByIngredient($ingredient);

        // Pour chaque recette
        foreach ($recipesIngredients as $recipeIngredient) {
            $recipe = $entityManager->getRepository(Recipe::class)->findWithIngredients($recipeIngredient->getRecipe()->getId());
            $recipe->setCalorie(0);
            $entityManager->persist($recipe);
        }

        $entityManager->flush();
    }

    /**
     * Redirection de la route en fonction de la route du filtre enregistré dans la session.
     *
     * @param SessionInterface
     *
     * @return Route
     */
    private function redirectToRoute(SessionInterface $session)
    {
        $route = $session->get('ingredient.filter.route');
        $route = (empty($route)) ? 'ingredient_index' : $route;

        return $this->redirectToRoute($route);
    }
}
