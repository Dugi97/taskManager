<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\File;
use App\Entity\Image;
use App\Entity\Post;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\SortDataService;
use App\Service\UploadService;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @return Response
     */
    public function showMyAccout(User $user): Response
    {
        $editProfileForm = $this->createForm(UserType::class, $user);

        return $this->render('user/my_profile.html.twig', [
            'user' => $user,
            'allPosts' => $user->getPosts(),
            'myImages' => $user->getFiles(),
            'editUserForm' => $editProfileForm->createView()
        ]);
    }

    /**
     * @Route("/edit/profile/{user}", name="editProfileData", methods={"POST"})
     * @param User $user
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function editProfileData(Request $request, User $user): RedirectResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/change/photo/{user}", name="change_photo", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function changePhoto(Request $request, User $user)
    {
        $imageId = $request->get('selectedImageId');
        $entityManager= $this->getDoctrine()->getManager();
        $imageObject = $entityManager->getRepository(File::class)->find($imageId);
        $user->setProfilePicture($imageObject->getUrl());
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/all/pictures/{user}", name="get_all_pictures")
     * @param User $user
     * @return Response
     */
    public function getAllPictures(User $user)
    {
        return $this->render('user/gallery.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/upload/profile/picture", name="upload_profile_picture")
     * @param UploadService $uploadService
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @throws Exception
     */
    public function uploadProfilePicture(UploadService $uploadService, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = new Post();
        /** @var User $this */
        $post->setUser($this->getUser());
        $dateAndTime = new DateTime();
        $post->setDateAndTime($dateAndTime->format('Y-m-d H:i:s'));
        $post->setText('New picture');
        $fileObject = $uploadService->uploadFile($request, $this->getUser(), $type = 'image', $post);
        $entityManager->persist($post);
        $entityManager->flush();
        $request->request->set('selectedImageId', $fileObject->getId());
        $this->changePhoto($request, $this->getUser());

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/send/message", name="send_message")
     * @param Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {
        $message = $request->get('message');


        return $this->render('user/gallery.html.twig', [
            'user' => $user,
        ]);
    }
}
