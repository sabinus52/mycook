<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\Recipe;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Classe pour le téléchargement des images des recettes.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
class RecipeUploader extends FileUploader
{
    /**
     * Chargement de l'image.
     */
    public function upload(UploadedFile $file, Recipe $recipe): bool
    {
        $this->removeCacheThumb((string) $recipe->getId());

        return $this->move($file, (string) $recipe->getId());
    }
}
