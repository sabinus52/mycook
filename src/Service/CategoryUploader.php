<?php
/**
 * Classe pour le téléchargement des images des catégories
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Service;

use App\Entity\Category;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class CategoryUploader extends FileUploader
{

    /**
     * Chargement de l'image
     */
    public function upload(UploadedFile $file, Category $category): bool
    {
        return $this->move($file, $category->getId().'.png');
    }
    
}