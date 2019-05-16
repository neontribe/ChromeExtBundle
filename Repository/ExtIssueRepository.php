<?php
namespace KimaiPlugin\ChromeExtBundle\Repository;

use KimaiPlugin\ChromeExtBundle\Entity\ExtProject;
use App\Repository\AbstractRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method ExtIssue|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExtIssue|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExtIssue[] findAll()
 * @method ExtIssue[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExtIssueRepository extends AbstractRepository {

  // /**
  // * @return ExtIssue[] Returns an array of ExtIssue objects
  // */
  /*
   * public function findByExampleField($value)
   * {
   * return $this->createQueryBuilder('e')
   * ->andWhere('e.exampleField = :val')
   * ->setParameter('val', $value)
   * ->orderBy('e.id', 'ASC')
   * ->setMaxResults(10)
   * ->getQuery()
   * ->getResult()
   * ;
   * }
   */

  /*
   * public function findOneBySomeField($value): ?ExtIssue
   * {
   * return $this->createQueryBuilder('e')
   * ->andWhere('e.exampleField = :val')
   * ->setParameter('val', $value)
   * ->getQuery()
   * ->getOneOrNullResult()
   * ;
   * }
   */
}
