<?php

namespace App\DataFixtures;

use App\Entity\Invoice;
use App\Entity\Quote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager): void
  {
    $faker = \Faker\Factory::create('fr_FR');

    $quotes = $manager->getRepository(Quote::class)->findAll();

    foreach ($quotes as $quote) {
      $invoice = new Invoice();
      $invoice->setQuote($quote);
      // set date between january 1st of the current year and today
      $invoice->setDate($faker->dateTimeBetween(date('Y-01-01'), 'now'));
      $invoice->setPaid($faker->boolean(50));
      $manager->persist($invoice);
    }

    $manager->flush();
  }

  public function getDependencies(): array
  {
    return [
      QuoteFixtures::class,
    ];
  }
}
