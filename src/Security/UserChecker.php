<?php

namespace App\Security;

use App\Entity\User;
use PHPUnit\Util\Exception;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

// Класс отвечающий за блокировку пользователей
class UserChecker implements UserCheckerInterface
{

    /**
     * @param User $user
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (null === $user->getBannedUntil())
        {
            // Если пользователь не забанен вернуть пользователя
            return ;
        }

        $now = new \DateTime();

        // В случае если указанная в колонке бана дата больше чем текущая дата, то доступ будет закрыт
        if ($now < $user->getBannedUntil())
        {
            throw new AccessDeniedHttpException('The user is banned');
        }

    }

    /**
     * @param User $user
     */
    public function checkPostAuth(UserInterface $user)
    {

    }
}