<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Post;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\SortDataService;
use App\Service\UploadService;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/user/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{accountAlias}", name="show_my_account", methods={"GET"})
     * @param User $user
     * @param SortDataService $sortDataService
     * @return Response
     */
    public function showMyAccout(User $user, SortDataService $sortDataService): Response
    {
        $allMyPosts = $this->getDoctrine()->getRepository(Post::class)->findBy(['user' => $this->getUser()->getId()]);
        $myImages = $this->getDoctrine()->getRepository(Image::class)->findBy(['postedBy' => $this->getUser()->getId()]);
        $sortedPosts = $sortDataService->sortPostData($allMyPosts);

        for ($i = 0; $i < count($sortedPosts); $i++) {
            $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(['post' => $sortedPosts[$i]['id']]);
            $sortedPosts[$i]['comments'] = $sortDataService->sortCommentData($comments);
        }

        return $this->render('user/my_profile.html.twig', [
            'user' => $user,
            'allPosts' => $sortedPosts,
            'myImages' => $myImages
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}", name="user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/upload/image/{user}", name="upload_image")
     * @param Request $request
     * @param UploadService $uploadService
     * @return Response
     */
    public function uploadImage(Request $request, UploadService $uploadService, User $user): Response
    {
        $uploadService->uploadFile($request, $user);

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
