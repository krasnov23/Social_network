<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FollowerController extends AbstractController
{
    #[Route('/follow/{id}', name: 'app_follow')]
    public function follow(User $userToFollow,ManagerRegistry $doctrine,Request $request): Response
    {

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Проверка на то что пользователь на которого мы собираемся подписаться не является нами.
        if ($userToFollow->getId() !== $currentUser->getId())
        {
            $currentUser->follow($userToFollow);

            // В данном случае мы не изменяет ни сущность текущего пользователя ни сущность того на кого мы подписываемся
            // Вместо этого у нас есть таблица в которой будет указан пользователь и на кого подписан этот пользователь
            // вот почему на самом деле мы не испольузем класс репозиторий для сохранения изменений в любой из этих сущностей

            // Вместо этого мы будем использовать так называемый doctrine entity manager который можно получить вызвав гет менеджер
            // метод flush - Он вызывается в каждом репозитории для фактической фиксации
            // изменений в базе данных, для выполнения фактического запроса.

            $doctrine->getManager()->flush();
        }

        // Возвращает на ту же страницу с которой и шел запрос
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    public function unfollow(User $userToUnfollow,ManagerRegistry $doctrine,Request $request): Response
    {

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Проверка на то что пользователь на которого мы собираемся подписаться не является нами.
        if ($userToUnfollow->getId() !== $currentUser->getId())
        {
            $currentUser->unfollow($userToUnfollow);
            $doctrine->getManager()->flush();
        }

        // Возвращает на ту же страницу с которой и шел запрос
        return $this->redirect($request->headers->get('referer'));
    }
}
