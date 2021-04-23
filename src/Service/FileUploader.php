<?php
/**
 * Classe abstraite pour le téléchargement de fichier
 *
 * @author Olivier <sabinus52@gmail.com>
 *
 * @package MyCook
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


abstract class FileUploader
{
    
    /**
     * Dossier de destination
     * 
     * @var String
     */
    private $directory;

    
    /**
     * Constructeur
     * 
     * @param String $directory : Dossier de destination
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }


    /**
     * Déplacement du fichier téléchargé dans le dossier cible
     * 
     * @param UploadedFile $sourceFile : Objet du fichier source
     * @param String $targetFile : Nom du fichier de destination
     */
    protected function move(UploadedFile $sourceFile, string $targetFile): bool
    {
        try {
            $sourceFile->move($this->directory, $targetFile);
        } catch (FileException $e) {
            return false;
        }
        return true;
    }

}