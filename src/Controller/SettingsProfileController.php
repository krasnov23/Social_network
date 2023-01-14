<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\ProfileImageType;
use App\Form\UserProfileType;
use App\Repository\UserProfileRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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

    #[Route('/settings/profile-image', name: 'app_settings_profile_image')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage(Request $request, SluggerInterface $slugger,UserRepository $users): Response
    {

        $form = $this->createForm(ProfileImageType::class);

        /** @var User $user */
        $user = $this->getUser();
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid())
        {
            // Возвращает значение полученного поля в виде ассоциативного массива, которое мы добавили в ProfileImageType
            // со всем параметрами такими как время загрузки, время выгрузки, формат итд
            $profileImageFile = $form->get('profileImage')->getData();

            // Если пользователь Загрузил фотографию
            if ($profileImageFile)
            {
                // Получает название файла
                $originalFileName = pathinfo(
                    // Получает имя загруженного файла
                    $profileImageFile->getClientOriginalName(),
                    // берет ту часть из полного пути, где только название
                    PATHINFO_FILENAME
                );


                // Генерирует персональное имя этого изображения, добавляет дефисы вместо нижних подчеркиваний и пробелов
                $safeFileName = $slugger->slug($originalFileName);

                // Если пользователи постоянно будут называть profile JPEG
                // uniqid() будет задавать ему персональный номер
                // метод guessExtension получает расширение файла
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $profileImageFile->guessExtension();


                try {

                    // Куда будет загруженно наше фото
                    $profileImageFile->move(
                        // profiles_directory строчка в services.yaml в которой указан путь куда будет сохранен файл
                        $this->getParameter('profiles_directory'),
                        // Используем имя нашего файла как newFileName
                        $newFileName
                    );
                    // Оставляем исключение для непредвиденных обстоятельств
                } catch (FileException $exception){
                }

                // getYes - get User Profile
                $profile = $user->getYes() ?? new UserProfile();
                // Задаем нашему профилю в базе данных
                // Поэтому помните, что сохранение абсолютного пути к файлу может считаться плохой практикой.
                // Не имеет значения, где он хранится, потому что если вы просто сохраните фактическое имя файла, то это будет проще
                // чтобы переместить все файлы в другой каталог или другое место
                $profile->setImage($newFileName);

                // Задаем пользователю профиль, это может сыграть очень большую роль при отсутствии задавания профиля
                // база данных пользователя просто сохранит пользователя как он есть, т.е без изменений
                $user->setYes($profile);

                $users->save($user,true);

                $this->addFlash('success','Your profile image was updated');

                return $this->redirectToRoute('app_settings_profile_image');
            }

        }

        return $this->render('settings_profile/profile_image.html.twig',
            [
                'form' => $form->createView(),
            ]);


    }

}
