<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Repository\UserProfileRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsProfileController extends AbstractController
{
    #[Route('/settings/profile', name: 'app_settings_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(Request $request,UserRepository $users): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Если для User еще не создан профиль с его данными, то он будет создан
        $userProfile = $currentUser->getYes() ?? new UserProfile();

        $form = $this->createForm(UserProfileType::class, $userProfile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userProfile = $form->getData();

            // Save this somehow
            $currentUser->setYes($userProfile);

            $users->save($currentUser,true);

            // Add the flash message

            $this->addFlash('success','Your user profile settings changed');

            // Redirect

            return $this->redirectToRoute('app_settings_profile');

        }


        return $this->render('settings_profile/profile.html.twig',
        [
            'form' => $form->createView(),
        ]);
    }
}
