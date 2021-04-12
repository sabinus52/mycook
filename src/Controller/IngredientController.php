<?php
/**
 * Controleur de la gestion des ingrédients
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ingredient")
 */
class IngredientController extends AbstractController
{

    /**
     * Index ou liste des ingrédients
     * 
     * @Route("/", name="ingredient_index", methods={"GET"})
     */
    public function index(IngredientRepository $ingredientRepository): Response
    {
        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredientRepository->findAll(),
        ]);
    }


    /**
     * Création d'un nouvel ingrédient
     * 
     * @Route("/create", name="ingredient_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ingredient);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', "L'ingrédient <strong>".$ingredient->getName()."</strong> a été ajouté avec succès");
            return $this->redirectToRoute('ingredient_index');
        }

        return $this->render('ingredient/edit.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Visualisation d'un ingrédient
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
     * Edition d'un ingrédient
     * 
     * @Route("/{id}/update", name="ingredient_update", methods={"GET","POST"})
     */
    public function update(Request $request, Ingredient $ingredient): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', "L'ingrédient <strong>".$ingredient->getName()."</strong> a été modifié avec succès");
            return $this->redirectToRoute('ingredient_index');
        }

        return $this->render('ingredient/edit.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Suppression d'un ingrédient
     * @Route("/{id}/delete", name="ingredient_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, Ingredient $ingredient): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ingredient->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ingredient);
            $entityManager->flush();
            $this->get('session')->getFlashBag()->add('success', "L'ingrédient <strong>".$ingredient->getName()."</strong> a été supprimé avec succès");
        }

        return $this->redirectToRoute('ingredient_index');
    }

}
