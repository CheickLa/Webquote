<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

   /**
    * @return Invoice[] Returns an array of Invoice objects
    */
    public function findByDate($year = null, $month = null): array
    {
      if ($year === null) {
        $year = (int) date('Y');
      }
      if ($month === null) {
        $month = (int) date('m');
      }

      $start = new \DateTimeImmutable("$year-$month-01T00:00:00");
      $end = $start->modify('last day of this month')->setTime(23, 59, 59);

      return $this->createQueryBuilder('invoice')
        ->where('invoice.date BETWEEN :start AND :end')
        ->andWhere('invoice.paid = true')
        ->setParameter('start', $start->format('Y-m-d H:i:s'))
        ->setParameter('end', $end->format('Y-m-d H:i:s'))
        ->getQuery()
        ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Invoice
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
