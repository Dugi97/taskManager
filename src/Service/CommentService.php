<?php

namespace App\Service;


use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommentService
{
    private $container;
    private  $entityManager;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    public function children(array $array, $parentId = 0)
    {
        $subtree = [];
        foreach ($array as $a) {
            if ($a['parent'] == $parentId) {
                $children = $this->children($array, $a['id']);

                if ($children) {
                    $a['children'] = $children;
                }

                $subtree[] = $a;
            }
        }

        return $subtree;
    }

    public function objectsToArray($objects)
    {
        $array = [];
        foreach ($objects as $object) {
            $array[] = [
                'id' => $object->getId(),
                'user' => $object->getUser(),
                'post' => $object->getPost(),
                'parent' => $object->getParent(),
                'text' => $object->getText(),
                'time' => $object->getTime(),
                'children' => []
            ];
        }

        return $array;
    }

    public function getComments($postId)
    {
        $post = $this->entityManager->getRepository(Post::class)->find($postId);
        $comments = $post->getComments()->getValues();
        $commentsArray = $this->objectsToArray($comments);

        return $this->children($commentsArray);
    }
}
