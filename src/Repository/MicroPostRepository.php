<?php

namespace App\Repository;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        return $this->createQueryBuilder('p')
            // Добавляет комментарии к конечному результату
            ->addSelect('c')
            // Соединяем таблицу MicroPost c Comments по столбу comments
            ->leftJoin('p.comments','c')
            // Сортируем по свойству created
            ->orderBy('p.created','DESC')
            // гетКвери возвращает новый объект гет резалт конечный метод для получения этого объекта
            ->getQuery()->getResult();*/

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
