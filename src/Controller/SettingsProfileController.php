<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Repository\UserProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsProfileController extends AbstractController
{
    #[Route('/settings/profile', name: 'app_settings_profile')]
    public function profile(Request $request,UserProfileRepository $usersProfiles): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $userProfile = $currentUser->getYes() ?? new UserProfile();

        $form = $this->createForm(UserProfileType::class, $userProfile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userProfile = $form->getData();
            // Save this somehow
            //$usersProfiles->save($userProfile);

            // Add the flash message


            // Redirect


        }


        return $this->render('settings_profile/profile.html.twig',
        [
            'form' => $form->createView(),
        ]);
    }
}
