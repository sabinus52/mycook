<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use App\Service\CategoryUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur de gestion des catégories.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
#[Route(path: '/category')]
class CategoryController extends AbstractController
{
    /**
     * Liste des catégories.
     */
    #[Route(path: '/', name: 'category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * Ajout d'une nouvelle catégorie.
     */
    #[Route(path: '/create', name: 'category_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $entityManager, CategoryUploader $fileUploader): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $image = $form->get('image')->getData();
            if ($image) {
                $fileUploader->upload($image, $category); // @phpstan-ignore argument.type
            }

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * Visualiser les recettes de la catégorie.
     */
    #[Route(path: '/{id}', name: 'category_show', methods: ['GET'])]
    public function show(Category $category, RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findByCategory($category);

        return $this->render('category/recipes.html.twig', [
            'category' => $category,
            'recipes' => $recipes,
        ]);
    }

    /**
     * Éditer la catégorie.
     */
    #[Route(path: '/{id}/update', name: 'category_update', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, Category $category, EntityManagerInterface $entityManager, CategoryUploader $fileUploader): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $fileUploader->upload($image, $category); // @phpstan-ignore argument.type
            }

            $entityManager->flush();

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * Supprime une catégorie.
     */
    #[Route(path: '/{id}', name: 'category_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        /** @psalm-suppress PossiblyNullOperand */
        if ($this->isCsrfTokenValid('delete'.$category->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', sprintf('La catégorie <strong>%s</strong> a été supprimée avec succès', (string) $category->getName()));
        }

        return $this->redirectToRoute('category_index');
    }
}
