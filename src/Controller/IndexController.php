<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controleur de la page principale.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'index_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Les catégories où il y a le plus de recettes
        $categories = $entityManager->getRepository(Category::class)->findMostRecipes(6); /** @phpstan-ignore-line */

        // Les recettes les plus populaires
        $recipes = $entityManager->getRepository(Recipe::class)->findMostPopular(6); /** @phpstan-ignore-line */

        // Recette au hasard
        $recipeRandom = $entityManager->getRepository(Recipe::class)->findOneRandom(); /** @phpstan-ignore-line */

        return $this->render('home.html.twig', [
            'random' => $recipeRandom,
            'recipes' => $recipes,
            'categories' => $categories,
        ]);
    }

    /**
     * Auto completion de recherche des recettes.
     */
    #[Route(path: '/autocomplete/{term}', options: ['expose' => true], name: 'autocomplete-search')]
    public function autocompleteRecipe(Request $request, RecipeRepository $recipeRepository): JsonResponse
    {
        $term = $request->get('term');

        $query = $recipeRepository->createQueryBuilder('r')
            ->andWhere('r.name LIKE :val')
            ->setParameter('val', '%'.$term.'%')
            ->getQuery()
        ;

        $result = $query->getResult(Query::HYDRATE_ARRAY);

        return $this->json($result);
    }
}
