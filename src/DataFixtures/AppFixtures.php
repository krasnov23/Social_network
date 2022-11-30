<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    // Метод ответственный за создание нового объекта и помещения его в базе данных
    public function load(ObjectManager $manager): void
    {
        $microPost1 = new MicroPost();
        $microPost1->setTitle('Welcome to Kazakhstan, bitches!');
        $microPost1->setText('Welcome to Kazakhstan, bitches!');
        $microPost1->setCreated(new DateTime());
        // Добавить новую строку в таблицу
        $manager->persist($microPost1);

        $microPost2 = new MicroPost();
        $microPost2->setTitle('Welcome to US, bitches!');
        $microPost2->setText('Welcome to US, bitches!');
        $microPost2->setCreated(new DateTime());
        // Добавить новую строку в таблицу
        $manager->persist($microPost2);

        $microPost3 = new MicroPost();
        $microPost3->setTitle('Welcome to Russia, bitches!');
        $microPost3->setText('Welcome to Russia, bitches!');
        $microPost3->setCreated(new DateTime());
        // Добавить новую строку в таблицу
        $manager->persist($microPost3);

        // Исполнить запрос
        $manager->flush();
    }
}
