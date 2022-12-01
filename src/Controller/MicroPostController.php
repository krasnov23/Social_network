<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Repository\MicroPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    // Передаем аргумент который и есть наша база данных
    public function index(MicroPostRepository $posts): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts->findAll(),
        ]);
    }

    // В переменную пост Вставляется номер поста и по айди выводятся соответственные данные
    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    public function showOne(MicroPost $post): Response
    {

        return $this->render('micro_post/show.html.twig',
        [ 'post' => $post ]);

    }

    #[Route('/micro-post/add', name: 'app_micro_post_add',priority: 2)]
    public function add(): Response
    {
        $microPost = new MicroPost();
        // Подготовка класса к созданию формы, добавление полей поля должны соответствовать свойствам класса
        $form = $this->createFormBuilder($microPost)
            ->add('title')->add('text')->add('submit',SubmitType::class,
                ['label' => 'Save'])->getForm();
        // label - атрибут обозначения кнопки submit

        // Передача формы в шаблон, в шаблоне переменная form вставляет в функцию для форм form
        return $this->renderForm("micro_post/add.html.twig",
        ["forma" => $form]);


    }

    // Команда для упрощения работы с выделенным айди
    // composer require sensio/framework-extra-bundle

    //return $this->render('micro');
    // https://symfony.com/bundles/SensioFrameworkExtraBundle/current/index.html
    // Также мы можем указать любое из свойств класса и по введению в адрес имени этого свойства будет выведен нужный нам объект
    // Как и по id (Например: title)

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
