<?php

declare(strict_types=1);

/**
 *  This file is part of MyCook Application.
 *  (c) Sabinus52 <sabinus52@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Service;

use Imagine\Gd\Imagine;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Classe abstraite pour le téléchargement de fichier.
 *
 * @author Olivier <sabinus52@gmail.com>
 */
abstract class FileUploader
{
    /**
     * Dossier racine de la zone publique.
     */
    private readonly string $rootDir;

    /**
     * Constructeur.
     *
     * @param string $directory : Dossier de destination des images
     */
    public function __construct(private readonly string $directory, KernelInterface $kernel, protected CacheManager $imagineCacheManager)
    {
        $this->rootDir = $kernel->getProjectDir().'/public';
    }

    /**
     * Déplacement du fichier téléchargé dans le dossier cible.
     *
     * @param UploadedFile $sourceFile : Objet du fichier source
     * @param string       $targetFile : Nom du fichier de destination
     */
    protected function move(UploadedFile $sourceFile, string $targetFile): bool
    {
        $fileTmp = $targetFile.'.'.$sourceFile->guessExtension();
        try {
            $sourceFile->move(sys_get_temp_dir(), $fileTmp);
            $this->transformToJPEG(sys_get_temp_dir().'/'.$fileTmp, $this->rootDir.$this->directory.'/'.$targetFile.'.jpg');
        } catch (FileException) {
            return false;
        }

        return true;
    }

    /**
     * Enregistre l'image au format JPEG.
     *
     * @param string $source : Chemin complet du fichier source
     * @param string $target : Chemin complet du fichier destination
     */
    public function transformToJPEG(string $source, string $target): void
    {
        $imagine = new Imagine();
        $imagine->open($source)->save($target, ['jpeg_quality' => 85]);
        try {
            unlink($source);
        } catch (\Throwable) {
            return;
        }
    }

    /**
     * Supprime la vignette en cache.
     *
     * @param string $targetFile : Nom du fichier de destination
     */
    protected function removeCacheThumb(string $targetFile): void
    {
        $this->imagineCacheManager->remove($this->directory.'/'.$targetFile.'.jpg');
    }
}
