<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    // Аргумент ниже вводится дли шифрования наших паролей
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {

    }



    // Метод ответственный за создание нового объекта и помещения его в базе данных
    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('test@test.com');
        $user1->setPassword($this->userPasswordHasher->hashPassword($user1,'12345678'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('john@test.com');
        $user2->setPassword($this->userPasswordHasher->hashPassword($user2,'12345678'));
        $manager->persist($user2);


        $microPost1 = new MicroPost();
        $microPost1->setTitle('Welcome to Kazakhstan, bitches!');
        $microPost1->setText('Welcome to Kazakhstan, bitches!');
        $microPost1->setCreated(new DateTime());
        $microPost1->setAuthor($user1);
        // Добавить новую строку в таблицу
        $manager->persist($microPost1);

        $microPost2 = new MicroPost();
        $microPost2->setTitle('Welcome to US, bitches!');
        $microPost2->setText('Welcome to US, bitches!');
        $microPost2->setCreated(new DateTime());
        $microPost2->setAuthor($user1);
        // Добавить новую строку в таблицу
        $manager->persist($microPost2);

        $microPost3 = new MicroPost();
        $microPost3->setTitle('Welcome to Russia, bitches!');
        $microPost3->setText('Welcome to Russia, bitches!');
        $microPost3->setCreated(new DateTime());
        // Добавить новую строку в таблицу
        $microPost3->setAuthor($user2);
        $manager->persist($microPost3);

        // Исполнить запрос
        $manager->flush();
    }
}
