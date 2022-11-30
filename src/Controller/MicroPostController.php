<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    // Передаем аргумент который и есть наша база данных
    public function index(MicroPostRepository $posts): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'controller_name' => 'MicroPostController',
        ]);
    }

    #[Route('/micro-post/{id<\d+>}', name: 'app_micro_post_show')]
    public function showOne($id, MicroPostRepository $posts): Response
    {
        $currentPost = $posts->find($id);
        dd($currentPost);
        return $this->render('micro');
    }

    // Находит все записи в таблице
    //dd($posts->findall());
    // Находит запись в таблице по айди
    // dd($posts->find(3));
    // Находит запись по названию
    // dd($posts->findBy(['title'=>'Welcome to Russia, bitches!']));

    // Добавление объекта в базу данных
    /*$microPost = new MicroPost();
    $microPost->setTitle('It comes from controller');
    $microPost->setText('Hi');
    $microPost->setCreated(new \DateTime());*/
    // Отправляем наш объект в MicroPost Repository, который в свою очередь отправляет их в базу данных.
    // $posts->save($microPost,true);

    // Изменение названия по id
    /*$microPost = $posts->find(4);
    $microPost->setTitle('Welcome in general');
    $posts->save($microPost,true);*/

    // Удаление из базы данных
    /*$microPost = $posts->find(4);
    $posts->remove($microPost,true);*/
}
