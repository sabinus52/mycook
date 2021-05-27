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
use Symfony\Component\HttpKernel\KernelInterface;
use \Liip\ImagineBundle\Imagine\Cache\CacheManager;


abstract class FileUploader
{
    
    /**
     * Dossier de destination des images
     * 
     * @var String
     */
    private $directory;

    /**
     * Dossier racine de la zone publique
     * 
     * @var String
     */
    private $rootDir;

    /**
     * @var CacheManager
     */
    protected $imagineCacheManager;

    
    /**
     * Constructeur
     * 
     * @param String $directory : Dossier de destination
     */
    public function __construct(string $directory, KernelInterface $kernel, CacheManager $imagineCacheManager)
    {
        $this->directory = $directory;
        $this->rootDir = $kernel->getProjectDir().'/public';
        $this->imagineCacheManager = $imagineCacheManager;
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
            $sourceFile->move($this->rootDir.$this->directory, $targetFile);
        } catch (FileException $e) {
            return false;
        }
        return true;
    }


    /**
     * Supprime la vignette en cache
     * 
     * @param String $targetFile : Nom du fichier de destination
     */
    protected function removeCacheThumb(string $targetFile)
    {
        $this->imagineCacheManager->remove($this->directory.'/'.$targetFile);
    }

}