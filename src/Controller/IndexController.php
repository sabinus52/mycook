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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur de la page principale.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index_home")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Les catégories où il y a le plus de recettes
        $categories = $entityManager->getRepository(Category::class)->findMostRecipes(6);

        // Les recettes les plus populaires
        $recipes = $entityManager->getRepository(Recipe::class)->findMostPopular(6);

        return $this->render('home.html.twig', [
            'recipes' => $recipes,
            'categories' => $categories,
        ]);
    }
}
