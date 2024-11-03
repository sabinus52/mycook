<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Idea;
use App\Form\IdeaType;
use App\Repository\IdeaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controleur de la gestion des idées de recettes.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
#[Route(path: '/idea')]
class IdeaController extends AbstractController
{
    /**
     * Index ou liste des ingrédients.
     */
    #[Route(path: '/', name: 'idea_index', methods: ['GET'])]
    public function index(IdeaRepository $ideaRepository): Response
    {
        return $this->render('idea/index.html.twig', [
            'ideas' => $ideaRepository->findAll(),
        ]);
    }

    /**
     * Création d'un nouvelle idée de recette.
     */
    #[Route(path: '/create', name: 'idea_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $idea = new Idea();
        $form = $this->createForm(IdeaType::class, $idea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($idea);
            $entityManager->flush();

            $this->addFlash('success', 'L\'idée de recette <strong>'.$idea->getName().'</strong> a été ajouté avec succès');

            return $this->redirectToRoute('idea_index');
        }

        return $this->renderForm('idea/edit.html.twig', [
            'idea' => $idea,
            'form' => $form,
        ]);
    }

    /**
     * Edition d'une idée de recette.
     */
    #[Route(path: '/{id}/update', name: 'idea_update', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Request $request, Idea $idea, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IdeaType::class, $idea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'idée de recette <strong>'.$idea->getName().'</strong> a été modifié avec succès');

            return $this->redirectToRoute('idea_index');
        }

        return $this->renderForm('idea/edit.html.twig', [
            'idea' => $idea,
            'form' => $form,
        ]);
    }

    /**
     * Suppression d'une idée de recette.
     */
    #[Route(path: '/{id}/delete', name: 'idea_delete', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Idea $idea, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$idea->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($idea);

            try {
                $entityManager->flush();
            } catch (\Exception) {
                $this->addFlash('danger', 'L\'idée recette <strong>'.$idea->getName().'</strong> ne peut pas être supprimé');

                return $this->redirectToRoute('idea_index');
            }

            $this->addFlash('success', 'L\'idée recette <strong>'.$idea->getName().'</strong> a été supprimé avec succès');
        }

        return $this->redirectToRoute('idea_index');
    }
}
