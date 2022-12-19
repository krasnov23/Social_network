<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    // Передаем аргумент который и есть наша база данных
    public function index(MicroPostRepository $posts): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts->findAllWithComments(),
        ]);
    }

    // В переменную пост Вставляется номер поста и по айди выводятся соответственные данные
    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    public function showOne(MicroPost $post): Response
    {

        return $this->render('micro_post/show.html.twig',
        [ 'post' => $post ]);

    }


    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    //#[IsGranted('ROLE_COMMENTER')]
    #[Route('/micro-post/add', name: 'app_micro_post_add',priority: 2)]
    public function add(Request $request,MicroPostRepository $posts): Response
    {
        // Еще один из вариантов ограничения доступа к чему либо внизу (Ограничивает доступ немедленно)
        //  Метод не дающий доступ к странице до тех пор, пока не пройдена авторизация
        //  Будет перенаправлен на страницу Авторизации
        //$this->denyAccessUnlessGranted(
            //'IS_AUTHENTICATED_FULLY'
            // Дает доступ ко всем
            // 'PUBLIC_ACCESS'
        //);
        //dd($this->getUser());
        $microPost = new MicroPost();
        // Подготовка класса к созданию формы, добавление полей поля должны соответствовать свойствам класса
        $form = $this->createForm(MicroPostType::class,$microPost);
        //->add('submit',SubmitType::class,
                //['label' => 'Save'])->getForm();
        // label - атрибут обозначения кнопки submit

        // данный метод получает данные которые будут отправлены во время запроса(т.е ввода данных)
        $form->handleRequest($request);

        // Проверка на то что форма подтверждена и соответствует условиям
        if ($form->isSubmitted() and $form->isValid())
        {
            $post = $form->getData();
            // Задает объекту класса MicroPost свойству created время которое сейчас
            $post->setCreated(new \DateTime());

            $post->setAuthor($this->getUser());

            // Отправляет данные в MicroPostRepository откуда они уже поступают в БД
            $posts->save($post,true);

            // Добавление уведомления "Успешно"
            $this->addFlash('success','Your micropost had been added');

            // Переход на следующую страницу после подтверждения формы
            return $this->redirectToRoute('app_micro_post');
            // Также можно сделать переход по следующему адресу return $this->redirect('/micro-post')

        }

        // Передача формы в шаблон, в шаблоне переменная form вставляет в функцию для форм form
        return $this->renderForm("micro_post/add.html.twig",
        ["forma" => $form]);

    }

    #[IsGranted('ROLE_EDITOR')]
    #[Route('/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    public function edit(MicroPost $post,Request $request,MicroPostRepository $posts): Response
    {

        $form = $this->createForm(MicroPostType::class,$post);

        // данный метод получает данные которые будут отправлены во время запроса(т.е ввода данных)
        $form->handleRequest($request);

        // Проверка на то что форма подтверждена и соответствует условиям
        if ($form->isSubmitted() and $form->isValid())
        {
            $post = $form->getData();
            // Получает данные в MicroPostRepository откуда они уже поступают в Поле
            $posts->save($post,true);

            // Добавление уведомления "Успешно"
            $this->addFlash('success','Your micropost had been updated');

            // Переход на следующую страницу после подтверждения формы
            return $this->redirectToRoute('app_micro_post');
            // Также можно сделать переход по следующему адресу return $this->redirect('/micro-post')

        }

        // Передача формы в шаблон, в шаблоне переменная form вставляет в функцию для форм form
        return $this->renderForm("micro_post/edit.html.twig",
            ["forma" => $form,
             "post" => $post]);

    }

    #[IsGranted('ROLE_COMMENTER')]
    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment')]
    public function addComment(MicroPost $post,Request $request,CommentRepository $comments): Response
    {

        $form = $this->createForm(CommentType::class,new Comment());
        $form->handleRequest($request);

        // Проверка на то что форма подтверждена и соответствует условиям
        if ($form->isSubmitted() and $form->isValid())
        {
            // Получает данные формы
            $comment = $form->getData();

            // Задает пост текущему комментарию
            $comment->setMicroPost($post);

            // Задаем Автора комментарию
            $comment->setAuthor($this->getUser());


            // Получает данные в MicroPostRepository откуда они уже поступают в Поле
            $comments->save($comment,true);

            // Добавление уведомления "Успешно"
            $this->addFlash('success','Your comment had been updated');

            // Переход на следующую страницу после подтверждения формы
            return $this->redirectToRoute('app_micro_post_show',['post'=> $post->getId()]);
            // Также можно сделать переход по следующему адресу return $this->redirect('/micro-post')

        }

        // Передача формы в шаблон, в шаблоне переменная form вставляет в функцию для форм form
        return $this->renderForm("micro_post/comment.html.twig",
            ["forma" => $form,
             "post" => $post]);

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
