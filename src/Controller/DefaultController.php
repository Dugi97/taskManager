<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('default/index.html.twig', [
            'current_user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function adminPanel()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @param Post $post
     * @return Response
     */
    public function getViewPictureModal(Post $post)
    {
        return $this->render('embed/view_picture_modal.html.twig', [
            'post' => $post
        ]);
    }
}
