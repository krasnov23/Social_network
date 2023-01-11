<?php

namespace App\Repository;

use App\Entity\MicroPost;
use App\Entity\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MicroPost>
 *
 * @method MicroPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method MicroPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method MicroPost[]    findAll()
 * @method MicroPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MicroPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    public function save(MicroPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MicroPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllWithComments(): array
    {

        // заменяем запрос указанный ниже
        return $this->findAllQuery(withComments: true)->getQuery()->getResult();

        /*// Ссылается на класс MicroPost
        // в скобках метода createQueryBuilder указан псевдоним который ссылается на текущую страницу
        return $this->createQueryBuilder('p')
            // Добавляет комментарии к конечному результату
            ->addSelect('c')
            // Соединяем таблицу MicroPost c Comments по столбу comments (дали псевдоним "c")
            // Если мы хотим чтобы добавились только те посты где есть комментарии
            ->leftJoin('p.comments','c')
            // Сортируем по свойству created (Когда был выложен пост)
            ->orderBy('p.created','DESC')
            // гетКвери возвращает новый объект гет резалт конечный метод для получения этого объекта
            ->getQuery()->getResult();*/

    }


    // Наш объект Майкропост не имеет свойства подсчета комментариев
    // поэтому мы используем агрегатные методы SQL
    // В данном случае используем having вместо where
    public function findAllWithMinLikes(int $minLikes)
    {
        // Включаем только посты в которых есть минимальное количество лайков в нашем случае больше 0.
        // $idList Выдает список айдишников объектов в которых лайков больше чем в указанном параметре
        $idList = $this->findAllQuery(withLikes: true)
            // Выбираем только id
            ->select('p.id')
            // Группируем по id чтобы выдавало все id которые проходят под наш критерий
            ->groupBy('p.id')
            // Хотим получить только посты как минимум имеющие один лайк, используем l потому что добавляли l как likes в методе выше
            // В данном случае используем having вместо where, т.к where не используется вместе с count.
            ->having('COUNT(l) > :minLikes')
            ->setParameter('minLikes',$minLikes)
            ->getQuery()
            // По скольку данный запрос нам выдаст только idшники (благодаря команде select id).
            // Он выдаст нам массив с массивами в которых будет указан только id. Указанный параметр Скалар Колом.
            // Выведет данные в виде одного массива с айдишниками
            ->getResult(Query::HYDRATE_SCALAR_COLUMN);
        // По скольку данный запрос будет выводить только по посту с указанием одного лайка и одного комментария это не совсем
        // то что мы хотим в итоге

        return $this->findAllQuery(
            withComments: true,withProfiles: true,withLikes: true,withAuthors: true
        )->where('p.id in (:idList)')->setParameter('idList',$idList)->getQuery()->getResult();
    }

    public function findAllByAuthors(int|User $author): array
    {
        // Запрос для отображения страницы конкретного пользователя и его постов для уменьшения количества отправляемых запросов
        // Где автор если передается как экземпляр класса то получаем его айди, а если айди то получаем сразу айди напрямую
        return $this->findAllQuery(withComments: true,withAuthors: true,withLikes: true,withProfiles: true)
            ->where('p.author = :author')
            ->setParameter('author',$author instanceof User ? $author->getId() : $author)
            ->getQuery()->getResult();
    }

    public function findAllPostsFromYourSubscribe(Collection|array $follows)
    {

        return $this->findAllQuery(withComments: true,withAuthors: true,withLikes: true,withProfiles: true)
            ->where('p.author IN (:follows)')->setParameter('follows',$follows)
            ->getQuery()->getResult();


    }

    private function findAllQuery(
        bool $withComments = false,
        bool $withLikes = false,
        bool $withAuthors = false,
        bool $withProfiles = false,

    ): QueryBuilder
    {
        $query = $this->createQueryBuilder('p');

        if ($withComments)
        {
            $query->leftJoin('p.comments','c')
                ->addSelect('c');
        }

        if ($withLikes)
        {
            $query->leftJoin('p.likedBy','l')
                ->addSelect('l');
        }


        if ($withAuthors || $withProfiles)
        {
            $query->leftJoin('p.author','a')
                ->addSelect('a');
        }

        if ($withProfiles)
        {
            // По скольку Профайл свойство класса User, а user это свойство author класса Micropost которое обозначается у нас
            // в запросе выше как a. up - сокращенно UserProfile
            $query->leftJoin('a.yes','up')
                ->addSelect('up');
        }


        return $query->orderBy('p.created','DESC');

    }

//    /**
//     * @return MicroPost[] Returns an array of MicroPost objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MicroPost
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
