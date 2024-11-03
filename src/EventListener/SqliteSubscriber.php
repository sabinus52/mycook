<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;

/**
 * Class SqlitePreFlushSubscriber paour activer les clés étrangères.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
final class SqliteSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [Events::preFlush];
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        if ($args->getEntityManager()->getConnection()->getDatabasePlatform() instanceof SqlitePlatform) {
            $args->getEntityManager()->getConnection()->executeStatement('PRAGMA foreign_keys = ON;');
        }
    }
}
