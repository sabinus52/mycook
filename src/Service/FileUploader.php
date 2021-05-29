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
use \Imagine\Gd\Imagine;


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
        $fileTmp = $targetFile.'.'.$sourceFile->guessExtension();
        try {
            $sourceFile->move(sys_get_temp_dir(), $fileTmp);
            $this->transformToJPEG(sys_get_temp_dir().'/'.$fileTmp, $this->rootDir.$this->directory.'/'.$targetFile.'.jpg');
        } catch (FileException $e) {
            return false;
        }
        return true;
    }


    /**
     * Enregistre l'image au format JPEG
     * 
     * @param String $source : Chemin complet du fichier source
     * @param String $target : Chemin complet du fichier destination
     */
    public function transformToJPEG(string $source, string $target): void
    {
        $imagine = new Imagine();
        $imagine->open($source)->save($target, [ 'jpeg_quality' => 85 ]);
        @unlink($source);
    }


    /**
     * Supprime la vignette en cache
     * 
     * @param String $targetFile : Nom du fichier de destination
     */
    protected function removeCacheThumb(string $targetFile): void
    {
        $this->imagineCacheManager->remove($this->directory.'/'.$targetFile.'.jpg');
    }

}