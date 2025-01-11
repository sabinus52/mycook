<?php

declare(strict_types=1);

/**
 * This file is part of MyCook Application.
 * (c) Sabinus52 <sabinus52@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\Category;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Classe pour le téléchargement des images des catégories.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class CategoryUploader extends FileUploader
{
    /**
     * Chargement de l'image.
     */
    public function upload(UploadedFile $file, Category $category): bool
    {
        $this->removeCacheThumb((string) $category->getId());

        return $this->move($file, (string) $category->getId());
    }
}
