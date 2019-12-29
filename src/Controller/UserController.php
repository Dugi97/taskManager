<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\File;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    /**
     * @Route("/user/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function newUser(Request $request): Response
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
    public function showUser(User $user): Response
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
        $allMyPosts = $this->getDoctrine()->getRepository(Post::class)->findBy([
            'user' => $this->getUser()->getId()
        ]);
        $myImages = $this->getDoctrine()->getRepository(File::class)->findBy([
            'uploaded_by' => $this->getUser()->getId(),
            'type' => "image"
        ]);
        $sortedPosts = $sortDataService->sortPostData($allMyPosts);
        $sortedPostsData = $this->sortPostData($sortDataService, $sortedPosts);

        return $this->render('user/my_profile.html.twig', [
            'user' => $user,
            'allPosts' => $sortedPostsData,
            'myImages' => $myImages
        ]);
    }

    /**
     * @param SortDataService $sortDataService
     * @param $sortedPosts
     * @return mixed
     */
    public function sortPostData($sortDataService, $sortedPosts)
    {
        for ($i = 0; $i < count($sortedPosts); $i++) {
            $allComments = $this->getDoctrine()->getRepository(Comment::class)->findBy(['post' => $sortedPosts[$i]['id']]);
            $allImages = $this->getDoctrine()->getRepository(File::class)->findBy(['post' => $sortedPosts[$i]['id'] ]);
            $sortedPosts[$i]['comments'] = $sortDataService->sortCommentData($allComments);
            $sortedPosts[$i]['images'] = $sortDataService->sortImagesData($allImages);

        }

        return $sortedPosts;
    }
}
