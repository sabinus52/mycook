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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur de la gestion des ingrédients.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
            'ingredients' => $ingredientRepository->findByCalorie(null), // @phpstan-ignore-line
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
        $ingredient = $ingredientRepository->findOneByName($request->get('term')); // @phpstan-ignore-line

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
    public function createFromRecipe(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientHiddenType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function create(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ingredient);
            $entityManager->flush();

            $this->updateRecipeCalories($ingredient, $entityManager);

            $this->addFlash('success', "L'ingrédient <strong>".$ingredient->getName().'</strong> a été ajouté avec succès');

            return $this->redirectToRoute2($session->get('ingredient.filter.route'));
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
    public function update(Request $request, Ingredient $ingredient, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->updateRecipeCalories($ingredient, $entityManager);

            $this->addFlash('success', "L'ingrédient <strong>".$ingredient->getName().'</strong> a été modifié avec succès');

            return $this->redirectToRoute2($session->get('ingredient.filter.route'));
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
    public function delete(Request $request, Ingredient $ingredient, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ingredient->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($ingredient);

            try {
                $entityManager->flush();
            } catch (Exception $th) {
                $this->addFlash('danger', "L'ingrédient <strong>".$ingredient->getName().'</strong> ne peut pas être supprimé');

                return $this->redirectToRoute('ingredient_index');
            }

            $this->addFlash('success', "L'ingrédient <strong>".$ingredient->getName().'</strong> a été supprimé avec succès');
        }

        return $this->redirectToRoute('ingredient_index');
    }

    /**
     * Mise à jour des calories suite à une mise à jour d'un ingrédient.
     */
    private function updateRecipeCalories(Ingredient $ingredient, EntityManagerInterface $entityManager): void
    {
        // Récupération des recettes avec l'ingrédient
        $recipesIngredients = $entityManager->getRepository(RecipeIngredient::class)->findByIngredient($ingredient); // @phpstan-ignore-line

        // Pour chaque recette
        foreach ($recipesIngredients as $recipeIngredient) {
            $recipe = $entityManager->getRepository(Recipe::class)->findWithIngredients($recipeIngredient->getRecipe()->getId()); // @phpstan-ignore-line
            $recipe->setCalorie(0);
            $entityManager->persist($recipe);
        }

        $entityManager->flush();
    }

    /**
     * Redirection de la route en fonction de la route du filtre enregistré dans la session.
     */
    protected function redirectToRoute2(string $route): RedirectResponse
    {
        $route = (empty($route)) ? 'ingredient_index' : $route;

        return $this->redirectToRoute($route);
    }
}
