<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    private array $messages = ['Hello','Bye','Hi','Try'];

    // После знака вопроса указывается значение по умолчанию которое будет грузиться в исполнении ниже в случае если
    // Никакого значения не будет заданно
    // Если будет указанно 1 - будет выведенно Hello
    // Если будет указанно 2 - будет выведенно Hello,Bye
    #[Route('/{limit?3}',name: 'app_index')]
    public function index(int $limit): Response
    {
        return $this->render('hello/index.html.twig',
            // Выводит массив с 0 символа если $limit = 1 , только нулевой, если 2 то нулевой и первый и так далее
            ['messages'=> array_slice($this->messages,0, $limit)]);
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
