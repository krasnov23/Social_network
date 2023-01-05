<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function index(User $user,MicroPostRepository $posts, Request $request): Response
    {
        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'currentUser' => $this->getUser(),
            // Метод который ищет посты по определенному автору
            'posts' => $posts->findAllByAuthors($user)
        ]);
    }

    #[Route('/profile/{id}/follows', name: 'app_profile_follows')]
    public function follows(User $user,Request $request): Response
    {


        return $this->render('profile/follows.html.twig', [
            'user' => $user,
            'currentUser' => $this->getUser()
        ]);
    }

    #[Route('/profile/{id}/followers', name: 'app_profile_followers')]
    public function followers(User $user,Request $request): Response
    {


        return $this->render('profile/followers.html.twig', [
            'user' => $user,
            'currentUser' => $this->getUser()
        ]);
    }


}
