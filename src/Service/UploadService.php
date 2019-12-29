<?php

namespace App\Service;


use App\Entity\File;
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
     * @param $type
     * @param $size
     */
    public function createAndSaveFileObject($user, $name, $uniqueName, $type, $size, $post): void
    {
        $fileObject = new File();
        $fileObject->setUploadedBy($user);
        $fileObject->setName($name);
        $fileObject->setUniqueName($uniqueName);
        $fileObject->setUrl('/uploads/files/'.$uniqueName);
        $fileObject->setSize($size);
        $fileObject->setTime(date('d/m/Y H:i:s'));
        $fileObject->setType($type);
        $fileObject->setPost($post);
        $this->entityManager->persist($fileObject);
        $this->entityManager->flush();
    }

    /**
     * @param $request
     * @param $user
     * @param $type
     * @param $post
     * @return JsonResponse
     */
    public function uploadFile($request, $user, $type, $post)
    {
        $files = $request->files->get('files');
        foreach ($files as $file) {
            /** @var UploadedFile $file */
            $filename = $file->getClientOriginalName();
            $uniqueName = $getfilename =  str_replace(' ', '_', $this->generateUniqueFileName().'-'.$file->getClientOriginalName());
            $size = $file->getSize();
            $file->move(
                $this->container->getParameter('files_directory'),
                $uniqueName
            );
            $this->createAndSaveFileObject($user, $filename, $uniqueName, $type, $size, $post);
        }

        return new JsonResponse(true);
    }
}
