<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\File;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\UploadService;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/new", name="post_new", methods={"GET","POST"})
     * @param Request $request
     * @param UploadService $uploadService
     * @return Response
     * @throws Exception
     */
    public function new(Request $request, UploadService $uploadService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = new Post();
        $post->setText($request->get('newPost'));
        $post->setUser($this->getUser());
        $dateAndTime = new DateTime();
        $post->setDateAndTime($dateAndTime->format('Y-m-d H:i:s'));
        $uploadService->uploadFile($request, $this->getUser(), $type = 'image', $post);
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/new/comment/{postId}", name="leave_comment", methods={"GET","POST"})
     * @param Request $request
     * @param $postId
     * @return RedirectResponse
     * @throws Exception
     */
    public function newComment(Request $request, $postId)
    {
        $comment = new Comment();
        $comment->setPost($this->getDoctrine()->getManager()->getRepository(Post::class)->find($postId));
        $comment->setText($request->get('commentInputField'));
        $comment->setUser($this->getUser());
        $dateAndTime = new DateTime();
        $comment->setTime($dateAndTime->format('Y-m-d H:i:s'));
        $this->getDoctrine()->getManager()->persist($comment);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/{id}", name="post_show", methods={"GET"})
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
}
