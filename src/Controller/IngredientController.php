<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur de la gestion des ingrédients.
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
#[Route(path: '/ingredient')]
class IngredientController extends AbstractController
{
    /**
     * Index ou liste des ingrédients.
     */
    #[Route(path: '/', name: 'ingredient_index', methods: ['GET'])]
    public function index(IngredientRepository $ingredientRepository, SessionInterface $session, Request $request): Response
    {
        $session->set('ingredient.filter.route', $request->attributes->get('_route'));

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredientRepository->findAll(),
        ]);
    }

    /**
     * Index ou liste des ingrédients avec le champs "calorie" non rempli.
     */
    #[Route(path: '/without-calories', name: 'ingredient_index_without_cal', methods: ['GET'])]
    public function indexWithoutCalories(IngredientRepository $ingredientRepository, SessionInterface $session, Request $request): Response
    {
        $session->set('ingredient.filter.route', $request->attributes->get('_route'));

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredientRepository->findBy(['calorie' => null]),
        ]);
    }

    /**
     * Index ou liste des ingrédients.
     */
    #[Route(path: '/{term}.json', name: 'ingredient_json', options: ['expose' => true])]
    public function fetchFormatJSON(Request $request, IngredientRepository $ingredientRepository): JsonResponse
    {
        return new JsonResponse($ingredientRepository->searchByName($request->query->getString('term', ''), Query::HYDRATE_ARRAY));
    }

    /**
     * Retourne si un ingredient existe ou pas.
     */
    #[Route(path: '/is-exists', name: 'ingredient_isexists', options: ['expose' => true])]
    public function isExists(Request $request, IngredientRepository $ingredientRepository): JsonResponse
    {
        $ingredient = $ingredientRepository->findOneBy(['name' => $request->query->get('term')]);

        if (!$ingredient) {
            return new JsonResponse(['id' => 0, 'name' => $request->query->get('term'), 'unity' => '']);
        }

        return new JsonResponse([
            'id' => $ingredient->getId(),
            'name' => $ingredient->getName(),
            'unity' => $ingredient->getUnity()->getValue(),
        ]);
    }

    /**
     * Création d'un nouvel ingrédient depuis le formulaire de la recette.
     */
    #[Route(path: '/create-ajax', name: 'ingredient_create_from_recipe', methods: ['POST'], options: ['expose' => true])]
    #[IsGranted('ROLE_ADMIN')]
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
     */
    #[Route(path: '/create', name: 'ingredient_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ingredient);
            $entityManager->flush();

            $this->updateRecipeCalories($ingredient, $entityManager);

            $this->addFlash('success', sprintf("L'ingrédient <strong>%s</strong> a été ajouté avec succès", (string) $ingredient->getName()));

            return $this->redirectToRoute2((string) $session->get('ingredient.filter.route')); // @phpstan-ignore cast.string
        }

        return $this->render('ingredient/edit.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Visualisation d'un ingrédient.
     */
    #[Route(path: '/{id}', name: 'ingredient_show', methods: ['GET'])]
    public function show(Ingredient $ingredient): Response
    {
        return $this->render('ingredient/show.html.twig', [
            'ingredient' => $ingredient,
        ]);
    }

    /**
     * Edition d'un ingrédient.
     */
    #[Route(path: '/{id}/update', name: 'ingredient_update', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, Ingredient $ingredient, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->updateRecipeCalories($ingredient, $entityManager);

            $this->addFlash('success', sprintf('L\'ingrédient <strong>%s</strong> a été modifié avec succès', (string) $ingredient->getName()));

            return $this->redirectToRoute2((string) $session->get('ingredient.filter.route')); // @phpstan-ignore cast.string
        }

        return $this->render('ingredient/edit.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Suppression d'un ingrédient.
     */
    #[Route(path: '/{id}/delete', name: 'ingredient_delete', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Ingredient $ingredient, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.(int) $ingredient->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($ingredient);

            try {
                $entityManager->flush();
            } catch (\Exception) {
                $this->addFlash('danger', sprintf('L\'ingrédient <strong>%s</strong> ne peut pas être supprimé', (string) $ingredient->getName()));

                return $this->redirectToRoute('ingredient_index');
            }

            $this->addFlash('success', sprintf('L\'ingrédient <strong>%s</strong> a été supprimé avec succès', (string) $ingredient->getName()));
        }

        return $this->redirectToRoute('ingredient_index');
    }

    /**
     * Mise à jour des calories de chaque recette suite à une mise à jour d'un ingrédient.
     */
    private function updateRecipeCalories(Ingredient $ingredient, EntityManagerInterface $entityManager): void
    {
        // Récupération des recettes avec l'ingrédient
        $recipesIngredients = $entityManager->getRepository(RecipeIngredient::class)->findBy(['ingredient' => $ingredient]);

        // Pour chaque recette trouvée, on à jour les calories
        foreach ($recipesIngredients as $recipeIngredient) {
            $recipe = $recipeIngredient->getRecipe();
            if (!$recipe instanceof Recipe) {
                continue;
            }
            $recipe->setCalorie(0); // Forcer le calcul de la nouvelle valeur [HasLifecycleCallbacks]
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
