<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Données initiales des utilisateurs.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class UserFixtures extends Fixture
{
    /**
     * Constructeur.
     */
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    /**
     * Chargement des utilisateurs.
     */
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setName('Administrator');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'admin'));
        $manager->persist($user);

        $user = new User();
        $user->setUsername('guest');
        $user->setName('Guest');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'guest'));
        $manager->persist($user);

        $manager->flush();
    }
}
