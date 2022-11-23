<?php

namespace MainApp\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    private array $messages = ['Hello','Bye','Hi','Try'];


    // После знака вопроса указывается максимальный лимит который может в данном случае выдаваться в нашем массиве.
    // Если будет указанно 1 - будет выведенно Hello
    // Если будет указанно 2 - будет выведенно Hello,Bye
    #[Route('/{limit<\d+>?3}',name: 'app_index')]
    public function index(int $limit): Response
    {
        // Выводит срез массива в виде строки
        return new Response(implode(',',array_slice($this->messages,0, $limit)));
    }

    // Данный путь выводит на каждый из элементов в id
    // строчка <\d+> - Означает что
    #[Route("/messages/{id<\d+>}",name: 'app_show_one')]
    public function showOne(int $id): Response
    {
        return new Response($this->messages[$id]);
    }




}
