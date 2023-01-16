<?php

namespace App\Security\Voter;

use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MicroPostVoter extends Voter
{

    public function __construct(private Security $security)
    {

    }


    // Обычно subject объект базы данных, а attribute действие которое ты хочешь сделать над этой сущностью
    // Это не всегда так, но это типичный пример
    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        // Проверяет входит ли действие в одно из список действий
        return in_array($attribute, [MicroPost::EDIT, MicroPost::VIEW])
            // Проверяет является ли subject объектом класса майкропост
            // То есть является ли объектом базы данных
            && $subject instanceof MicroPost;
    }

    // Метод ниже будет вызван если метод выше вернет true
    /** @param MicroPost $subject */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Указываем тип данной переменной
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        // Если данные метод вернет false (user не является это значит что пользователю закрыт доступ к данному действию)
        // В данном случае потому что он не авторизован
        if (!$user instanceof UserInterface) {
            return false;
        }

        $isAuth = $user instanceof UserInterface;

        //
        if ($this->security->isGranted('ROLE_ADMIN'))
        {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            // Редактировать пост может только авторизованный пользователь который соответствует пользователю создавшему пост.
            case MicroPost::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                // Пользователь должен быть авторизован и юзер который создал данный пост должен быть тем же что и пользователь в данный момент
                return $isAuth && ($subject->getAuthor()->getId() === $user->getId() || $this->security->isGranted('ROLE_EDITOR'));

            case MicroPost::VIEW:
                if (!$subject->isExtraPrivacy())
                {
                    return true;
                }

                // Если пользователь Аутентифицирован и автор поста подписан на этого пользователя
                return $isAuth && ($subject->getAuthor()->getFollows()->contains($user) or $subject->getAuthor()->getId() === $user->getId());

                // logic to determine if the user can VIEW
                // return true or false
                // пишем return true потому что каждый может видеть пост
                //return true;
        }

        return false;
    }
}
