<?php

namespace App\Service;


use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

class SortDataService
{
    private $container;
    private  $entityManager;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $posts
     * @return array
     */
    public function sortPostData($posts)
    {
        $sortResult = [];
        foreach ($posts as $post) {
            $object['id'] = $post->getId();
            $object['user'] = $post->getUser();
            $object['text'] = $post->getText();
            $object['time'] = $post->getDateAndTime();
            $object['likes'] = $post->getLikes();

            array_push($sortResult, $object);
        }

        return $sortResult;
    }

    public function sortCommentData($comments)
    {
        $sortResult = [];
        foreach ($comments as $comment) {
            $object['id'] = $comment->getId();
            $object['user'] = $comment->getUser()->getFullName();
            $object['post'] = $comment->getPost()->getId();
            $object['text'] = $comment->getText();
            $object['time'] = $comment->getTime();

            array_push($sortResult, $object);
        }

        return $sortResult;
    }
    public function sortImagesData($images)
    {
        $sortResult = [];
        foreach ($images as $image) {
            $object['id'] = $image->getId();
            $object['user'] = $image->getUploadedBy();
            $object['url'] = $image->getUrl();

            array_push($sortResult, $object);
        }

        return $sortResult;
    }
}
