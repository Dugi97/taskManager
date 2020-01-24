<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\File;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Service\CommentService;
use App\Service\UploadService;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
        $requestPostText = $request->get('newPost');
        $postText = isset($requestPostText)? $requestPostText : '';
        $post->setText($postText);

        /** @var User $this */
        $post->setUser($this->getUser());
        $dateAndTime = new DateTime();
        $post->setDateAndTime($dateAndTime->format('Y-m-d H:i:s'));
        $uploadService->uploadFiles($request, $this->getUser(), $post, null);
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/new/comment/{postId}/{parentId}", name="leave_comment", methods={"GET","POST"})
     * @param Request $request
     * @param $postId
     * @param $parentId
     * @return RedirectResponse
     * @throws Exception
     */
    public function newComment(Request $request, $postId, $parentId = null): RedirectResponse
    {
        $comment = new Comment();
        $parentId = $request->get('parentId');

        /** @var Post $entityManager */
        $entityManager = $this->getDoctrine()->getManager();
        $comment->setPost($entityManager->getRepository(Post::class)->find($postId));
        $comment->setText($request->get('commentInputField'));

        /** @var User $this */
        $comment->setUser($this->getUser());
        !empty($parentId) ? $comment->setParent($parentId) : $comment->setParent(0);
        $this->getDoctrine()->getManager()->persist($comment);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/{id}", name="post_show", methods={"GET"})
     * @param Post $post
     * @return Response
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    /**
     * @Route("/comments/{postId}", name="get_comments", methods={"POST"})
     * @param CommentService $commentService
     * @param Request $request
     * @param $postId
     * @return Response
     */
    public function getComments(CommentService $commentService, Request $request, $postId)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->findOneBy(['id' => $postId]);
        $commentRepo = $this->getDoctrine()->getRepository(Comment::class);
        $comments = $commentRepo->returnCommentsOffset($postId, $request->get('offset'));
        $commentsArray = $commentService->children($commentService->objectsToArray($comments));

        return $this->render('embed/show_comments.html.twig', [
            'post' => $post,
            'comments' => $commentsArray
        ]);
    }

    /**
     * @Route("/comment/replays/{commentId}", name="get_replays", methods={"POST"})
     * @param Request $request
     * @param $commentId
     * @return Response
     */
    public function getReplays(Request $request, $commentId)
    {
        $comment = $this->getDoctrine()->getRepository(Comment::class)->findOneBy(['id' => $commentId]);
        $commentRepo = $this->getDoctrine()->getRepository(Comment::class);
        $replays = $commentRepo->returnReplaysOffset($commentId, $request->get('offset'));

        return $this->render('embed/show_replay.html.twig', [
            'replays' => $replays
        ]);
    }

    /**
     * @Route("/delete/{postId}", name="delete_post", methods={"GET"})
     * @param Request $request
     * @param $postId
     * @return RedirectResponse
     */
    public function deletePost(Request $request, $postId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository(Post::class)->find($postId);
        $relatedReplays = $entityManager = $entityManager->getRepository(Comment::class)->findBy(['post' => $postId]);
        dd($relatedReplays);
//        foreach ($relatedFiles as $file) {
//            $entityManager->remove($file);
//        }
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
