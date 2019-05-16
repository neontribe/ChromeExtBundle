<?php
namespace KimaiPlugin\ChromeExtBundle\Repository;

use KimaiPlugin\ChromeExtBundle\Entity\ExtProject;
use App\Repository\AbstractRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method ExtProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExtProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExtProject[] findAll()
 * @method ExtProject[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExtProjectRepository extends AbstractRepository {

  // /**
  // * @return ExtProject[] Returns an array of ExtProject objects
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
   * public function findOneBySomeField($value): ?ExtProject
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
