<?php

namespace App\Service;


use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

class UploadService
{
    private $container;
    private  $entityManager;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }


    /**
     * @param $user
     * @param $name
     * @param $uniqueName
     * @param $size
     */
    public function createAndSaveFile($user, $name, $uniqueName, $size): void
    {
        $fileObject = new Image();
        $fileObject->setPostedBy($user);
        $fileObject->setImageName($name);
        $fileObject->setUniqueName($uniqueName);
        $fileObject->setUrl('/build/images/'.$uniqueName);
        $fileObject->setSize($size);
        $fileObject->setUploadedTime(date('d/m/Y H:i:s'));
        $this->entityManager->persist($fileObject);
        $this->entityManager->flush();
    }

    /**
     * @param $request
     * @param $user
     * @return JsonResponse
     */
    public function uploadFile($request, $user)
    {
        $files = $request->files->get('files');
        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $filename = $file->getClientOriginalName();
            $uniqueName = $getfilename =  str_replace(' ', '_', $this->generateUniqueFileName().'-'.$file->getClientOriginalName());
            $size = $file->getSize();
            $file->move(
                __DIR__.'/../../public/build/images',
                $uniqueName
            );
            $this->createAndSaveFile($user, $filename, $uniqueName, $size);
        }

        return new JsonResponse('./../public/build/images/'.$uniqueName);
    }
}
