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
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Service\RecipeUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur de la gestion des recettes.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
#[Route(path: '/recipe')]
class RecipeController extends AbstractController
{
    /**
     * Index ou liste des recettes.
     */
    #[Route(path: '/', name: 'recipe_index', methods: ['GET'])]
    public function index(RecipeRepository $recipeRepository): Response
    {
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipeRepository->findAll(),
        ]);
    }

    /**
     * Création d'une recette.
     */
    #[Route(path: '/create', name: 'recipe_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $entityManager, RecipeUploader $fileUploader): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recipe);
            $entityManager->flush();

            $image = $form->get('image')->getData();
            if ($image) {
                $fileUploader->upload($image, $recipe); // @phpstan-ignore argument.type
            }

            $this->setPopularityUnityToIngredient($entityManager);

            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
        }

        $formIngredient = $this->createForm(IngredientHiddenType::class, new Ingredient());

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
            'formingredient' => $formIngredient->createView(),
        ]);
    }

    /**
     * Visualisation d'une recette.
     */
    #[Route(path: '/{id}', name: 'recipe_show', methods: ['GET'], options: ['expose' => true])]
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipe/details.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * Edition d'une recette.
     */
    #[Route(path: '/{id}/update', name: 'recipe_update', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, Recipe $recipe, EntityManagerInterface $entityManager, RecipeUploader $fileUploader): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $fileUploader->upload($image, $recipe); // @phpstan-ignore argument.type
            }

            $entityManager->flush();

            $this->setPopularityUnityToIngredient($entityManager);

            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
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
     * Suppression d'une recette.
     */
    #[Route(path: '/{id}', name: 'recipe_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.(int) $recipe->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($recipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('recipe_index');
    }

    /**
     * Mets à jour les unités les plus utilisées par ingrédient.
     */
    private function setPopularityUnityToIngredient(EntityManagerInterface $entityManager): void
    {
        // Récupération des tous les ingrédients
        $ingredients = $entityManager
            ->getRepository(Ingredient::class)
            ->findAll()
        ;

        /**
         * Récupération par ingrédient l'unité la plus utilisée.
         *
         * @phpstan-ignore-next-line
         */
        $ingredientsByUnity = $entityManager
            ->getRepository(RecipeIngredient::class)
            ->findMostPopularityUnityByIngredient()
        ;

        foreach ($ingredients as $ingredient) {
            // Pas encore d'ingrédient utilisé
            if (!isset($ingredientsByUnity[$ingredient->getId()])) {
                continue;
            }

            /** @psalm-suppress PossiblyNullArrayOffset */
            $unity = $ingredientsByUnity[$ingredient->getId()];
            // Si changement d'unité, on met à jour l'unité la plus utilisée
            if ($unity !== $ingredient->getUnity()) {
                $ingredient->setUnity($unity);
                $entityManager->persist($ingredient);
            }
        }
        $entityManager->flush();
    }
}
