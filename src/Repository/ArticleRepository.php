<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(
        private ManagerRegistry $registry,
        private PaginatorInterface $paginator
    ) {
        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLastestArticlesWithLimit(int $limit): array
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'u', 'i')
            ->join('a.user', 'u')
            ->leftJoin('a.articleImages', 'i')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findSearch(SearchData $search, bool $isActive): PaginationInterface
    {
        $query = $this->createQueryBuilder('a')
            ->select('a', 't', 'u', 'i', 'co')
            ->leftJoin('a.tags', 't')
            ->leftJoin('a.comments', 'co')
            ->join('a.user', 'u')
            ->leftJoin('a.articleImages', 'i');

        if ($isActive) {
            $query->andWhere('a.active = true');
        }

        if (!empty($search->getQuery())) {
            $query->andWhere('a.title LIKE :title')
                ->setParameter('title', '%' . $search->getQuery() . '%');
        }
        if (!empty($search->getTags())) {
            $query->andWhere('t.id IN (:tag)')
                ->setParameter('tag', $search->getTags());
        }

        $query->orderBy('a.createdAt', 'DESC');

        return $this->paginator->paginate(
            $query->getQuery(),
            $search->getPage(),
            6
        );
    }
}
