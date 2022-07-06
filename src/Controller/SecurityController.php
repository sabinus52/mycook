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
use App\Form\UserPasswordType;
use LogicException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
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
     *
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Déconnexion.
     *
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Changement du password.
     *
     * @Route("/change-password", name="app_change_password", methods={"GET", "POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();

            if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                $newEncodedPassword = $passwordEncoder->hashPassword($user, $user->getPlainPassword());
                $user->setPassword($newEncodedPassword);

                $this->addFlash('success', 'Votre mot de passe à bien été changé !');
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('app_change_password');
            }

            $this->addFlash('danger', 'Votre mot de passe n\'a pas pu être changé !');
        }

        return $this->render('security/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
