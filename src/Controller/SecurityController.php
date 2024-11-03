<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\User;
use Olix\BackOfficeBundle\Helper\Gravatar;
use Olix\BackOfficeBundle\Security\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controleur de la sécurité de l'application.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class SecurityController extends AbstractController
{
    /**
     * Connexion.
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Déconnexion.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Changement des informations du profil.
     */
    #[Route(path: '/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function changeProfile(Request $request, UserManager $manager): Response
    {
        // Utilisation de la classe UserManager
        /** @var User $user */
        $user = $this->getUser();
        $manager->setUser($user);
        $gravatar = new Gravatar();

        // Création du formulaire
        $form = $manager->createFormProfileUser();

        // Validation du formulaire de profile de l'utilisateur
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Update datas of this user
            $user->setAvatar($gravatar->get($user->getEmail()));
            $manager->setUser($form->getData())->update();
            $this->addFlash('success', 'La modification des informations a bien été prise en compte');

            return $this->redirectToRoute('app_profile');
        }

        return $this->renderForm('security/profile.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Changement du password.
     */
    #[Route(path: '/change-password', name: 'app_change_password', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function changePassword(Request $request, UserManager $manager): Response
    {
        // Utilisation de la classe UserManager
        /** @var User $user */
        $user = $this->getUser();
        $manager->setUser($user);

        // Création du formulaire
        $form = $manager->createFormProfilePassword();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $isError = false;

            if (!$form->isValid()) {
                $form->addError(new FormError('Nouveau mot de passe incorrect'));
                $isError = true;
            }
            if (!$manager->isPasswordValid($form->get('oldPassword')->getData())) {
                $form->addError(new FormError('Ancien mot de passe incorrect'));
                $isError = true;
            }
            if (!$isError) {
                // Change password for this user
                $manager->update($form->get('password')->getData());
                $this->addFlash('success', 'La modification du mot de passe a bien été prise en compte');

                return $this->redirectToRoute('app_change_password');
            }
        }

        return $this->renderForm('security/password.html.twig', [
            'form' => $form,
        ]);
    }
}
