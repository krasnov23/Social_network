<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use App\Repository\UserProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class HelloController extends AbstractController
{
    private array $messages = [['message' => 'Hello', 'created' => '2022/06/12'],
    ['message' => 'Hi', 'created' => '2022/04/12'],
    ['message' => 'Bye!', 'created' => '2021/05/12']];

    // После знака вопроса указывается значение по умолчанию которое будет грузиться в исполнении ниже в случае если
    // Никакого значения не будет заданно после слэша
    #[Route('/',name: 'app_index')]
    public function index(MicroPostRepository $posts,CommentRepository $comments): Response
    {

        // Для того чтобы создать MicroPost и Comment по отдельности нам не нужен дополнительный параметр cascade->persist
        /*$post = new MicroPost();
        $post->setTitle('Hello');
        $post->setText('Hello its me');
        $post->setCreated(new \DateTime());*/

        $post = $posts->find(11);

        $comment = new Comment();
        $comment->setText('Second Comment');
        $comment->setMicroPost($post);
        //$post->addComment($comment);
        $comments->save($comment,true);

        /*$user = new User();
        $user->setEmail('example@email.com');
        $user->setPassword('1234567890');*/
        // переменная profile находится в отношении к классу User 1:1 и без создания класса User
        //
        /*$profile = new UserProfile();
        $profile->setUser($user);
        $profiles->save($profile,true);*/

        // Находит и удаляет один из профилей (Соответственно удаляется как User так и UserProfile
        // т.к данные сущности не могут существовать друг без друга
        /*$profile = $profiles->find(1);
        $profiles->remove($profile,true);*/

        // Первый агрумент внутри метода render это адрес нашего шаблона относительно папки templates
        // второй элемент это массив с переменными которые будет передавать в данный шаблон
        return $this->render('hello/index.html.twig',
            // Выводит массив с 0 символа если $limit = 1 , только нулевой, если 2 то нулевой и первый и так далее
            // Для этого мы вводим термин slice внутри index.html,
            // т.е берем срез с нулевого по конкретный, который будет передан в ключе limit
            ['messages'=> $this->messages,
            'limit' => 3]);
    }
    // Шаблон base.html.twig встраивается в index.html.twig внутрь тега body

    // Данный путь выводит на каждый из элементов в id
    // строчка <\d+> - Означает что
    #[Route("/messages/{id<\d+>}",name: 'app_show_one')]
    public function showOne(int $id): Response
    {
        // $this->render указывает путь к шаблону
        // путь указывается относительно папки templates
        // Второй аргумент задается в виде массива который будет передаваться в виде данных в данный шаблон
        return $this->render(
          'hello/show_one.html.twig',
            ['message' => $this->messages[$id]]
        );
        //return new Response('<b>' . $this->messages[$id] . '</b>');
    }




}
