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
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * Autocompletion des Select2 en mode AJAX pour le formulaire de saisie de la recette.
     */
    #[Route(path: '/autocomplete', name: 'ingredient_autocomplete_select2')]
    public function getSearchAutoCompleteSelect2(Request $request, IngredientRepository $ingredientRepository): JsonResponse
    {
        // Paramètres du Request
        $term = $request->query->get('term', '');

        // Recherche des items
        /** @var Ingredient[] $ingredients */
        $ingredients = $ingredientRepository->createQueryBuilder('ingredient')
            ->andWhere('ingredient.name LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->orderBy('ingredient.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        // Mapping des résultats
        $results = [];
        foreach ($ingredients as $ingredient) {
            $results[] = [
                'id' => $ingredient->getId(),
                'text' => $ingredient->getName(),
                'unity' => $ingredient->getUnity()->value,
            ];
        }

        // Retourne tous les résultats
        return $this->json($results);
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
            'unity' => $ingredient->getUnity()->value,
        ]);
    }

    /**
     * Création d'un nouvel ingrédient depuis le formulaire de la recette.
     */
    #[Route(path: '/create-ajax', name: 'ingredient_create_from_recipe', methods: ['GET', 'POST'])]
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
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ingredient);
            $entityManager->flush();

            $this->updateRecipeCalories($ingredient, $entityManager);

            $this->addFlash('success', sprintf("L'ingrédient <strong>%s</strong> a été ajouté avec succès", (string) $ingredient->getName()));

            return new Response('OK');
        }

        return $this->render('@OlixBackOffice/Modal/form-vertical.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form,
            'modal' => [
                'title' => 'Ajouter un ingrédient',
                'btnlabel' => 'Ajouter',
            ],
        ]);
    }

    /**
     * Visualiser les recettes d'un ingrédient.
     */
    #[Route(path: '/{id}', name: 'ingredient_show', methods: ['GET'])]
    public function show(Ingredient $ingredient, RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findByIngredient($ingredient);

        return $this->render('ingredient/recipes.html.twig', [
            'ingredient' => $ingredient,
            'recipes' => $recipes,
        ]);
    }

    /**
     * Edition d'un ingrédient.
     */
    #[Route(path: '/{id}/update', name: 'ingredient_update', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, Ingredient $ingredient, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->updateRecipeCalories($ingredient, $entityManager);

            $this->addFlash('success', sprintf('L\'ingrédient <strong>%s</strong> a été modifié avec succès', $ingredient));

            return new Response('OK');
        }

        return $this->render('@OlixBackOffice/Modal/form-vertical.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form,
            'modal' => [
                'title' => 'Modifier un ingrédient',
                'btnlabel' => 'Mettre à jour',
            ],
        ]);
    }

    /**
     * Suppression d'un ingrédient.
     */
    #[Route(path: '/{id}/delete', name: 'ingredient_delete', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Ingredient $ingredient, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($ingredient);
            try {
                $entityManager->flush();
            } catch (\Exception) {
                $this->addFlash('danger', sprintf('L\'ingrédient <strong>%s</strong> ne peut pas être supprimé', $ingredient));

                return new Response('OK');
            }
            $this->addFlash('success', sprintf('L\'ingrédient <strong>%s</strong> a été supprimé avec succès', $ingredient));

            return new Response('OK');
        }

        return $this->render('@OlixBackOffice/Include/modal-content-delete.html.twig', [
            'form' => $form,
            'element' => sprintf('l\'ingrédient <strong>%s</strong>', $ingredient),
        ]);
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
}
