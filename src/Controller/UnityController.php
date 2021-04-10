<?php
/**
 * Controleur des types d'unités des quantités des ingrédients
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Controller;

use App\Entity\Unity;
use App\Form\UnityType;
use App\Repository\UnityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/unity")
 */
class UnityController extends AbstractController
{

    /**
     * Index ou liste des unités
     * 
     * @Route("/", name="unity_index", methods={"GET"})
     */
    public function index(UnityRepository $unityRepository): Response
    {
        return $this->render('unity/index.html.twig', [
            'unities' => $unityRepository->findAll(),
        ]);
    }

    /**
     * Création d'une nouvelle unité
     * 
     * @Route("/create", name="unity_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $unity = new Unity();
        $form = $this->createForm(UnityType::class, $unity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($unity);
            $entityManager->flush();

            return $this->redirectToRoute('unity_index');
        }

        return $this->render('unity/new.html.twig', [
            'unity' => $unity,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Visualisation d'une unité
     * 
     * @Route("/{id}", name="unity_show", methods={"GET"})
     */
    public function show(Unity $unity): Response
    {
        return $this->render('unity/show.html.twig', [
            'unity' => $unity,
        ]);
    }


    /**
     * Edition d'une unité
     * 
     * @Route("/{id}/edit", name="unity_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Unity $unity): Response
    {
        $form = $this->createForm(UnityType::class, $unity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('unity_index');
        }

        return $this->render('unity/edit.html.twig', [
            'unity' => $unity,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Suppression d'une unité
     * 
     * @Route("/{id}", name="unity_delete", methods={"POST"})
     */
    public function delete(Request $request, Unity $unity): Response
    {
        if ($this->isCsrfTokenValid('delete'.$unity->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($unity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('unity_index');
    }
}
