<?php
/**
 * Classe pour le téléchargement des images des recettes
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Service;

use App\Entity\Recipe;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class RecipeUploader extends FileUploader
{

    /**
     * Chargement de l'image
     */
    public function upload(UploadedFile $file, Recipe $recipe): bool
    {
        $this->removeCacheThumb($recipe->getId());
        return $this->move($file, $recipe->getId());
    }
    
}