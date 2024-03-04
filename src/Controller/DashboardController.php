<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use App\Repository\ServiceCategoryRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
  public function index(
    InvoiceRepository $invoiceRepository,
    ClientRepository $clientRepository,
    ServiceCategoryRepository $serviceCategoryRepository,
    ServiceRepository $serviceRepository,
    ChartBuilderInterface $chartBuilder,
    QuoteRepository $quoteRepository,
  ): Response
    {
        // Calculate monthly turnover
        $monthInvoices = $invoiceRepository->findByDate();

        // Filter by current user
        $monthInvoices = array_filter($monthInvoices, function ($invoice) {
          return $invoice->getQuote()->getClient()->getAgency() === $this->getUser()->getAgency();
        });

        $turnover = array_reduce($monthInvoices, function ($carry, $invoice) {
          return $carry + $invoice->getQuote()->getAmount();
        }, 0);

        // Get number of clients
        $clients = $clientRepository->findAll();
        $clients = array_filter($clients, function ($client) {
          return $client->getAgency() === $this->getUser()->getAgency();
        });
        $clientCount = count($clients);

        // Get number of service categories
        $serviceCategories = $serviceCategoryRepository->findAll();
        $serviceCategories = array_filter($serviceCategories, function ($serviceCategory) {
          return $serviceCategory->getAgency() === $this->getUser()->getAgency();
        });
        $serviceCategoryCount = count($serviceCategories);

        // Get number of services
        $services = $serviceRepository->findAll();
        $services = array_filter($services, function ($service) {
          return $service->getServiceCategory()->getAgency() === $this->getUser()->getAgency();
        });
        $serviceCount = count($services);

        // Chart of the turnover by month for this year 
        $monthlyTurnoverChart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $months = [
          'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre',
        ];
        $currentMonth = (int) date('m');
        $months = array_slice($months, 0, $currentMonth);
        
        $turnovers = [];
        foreach ($months as $month) {
          $monthInvoices = $invoiceRepository->findByDate(date('Y'), array_search($month, $months) + 1);

          // Filter by current user 
          $monthInvoices = array_filter($monthInvoices, function ($invoice) {
            return $invoice->getQuote()->getClient()->getAgency() === $this->getUser()->getAgency();
          });

          $turnover = array_reduce($monthInvoices, function ($carry, $invoice) {
            return $carry + $invoice->getQuote()->getAmount();
          }, 0);
          $turnovers[] = $turnover;
        } 

        $monthlyTurnoverChart->setData([
          'labels' => $months,
          'datasets' => [
            [
              'label' => 'Chiffre d\'affaires',
              'data' => $turnovers,
              'backgroundColor' => '#1F78D1',
              'borderColor' => '#1F78D1',
            ],
          ],
        ]);

        $monthlyTurnoverChart->setOptions([
          'maintainAspectRatio' => false,
          'plugins' => [
            'title' => [
              'display' => true,
              'text' => 'Chiffre d\'affaires mensuel',
            ],
            'legend' => [
              'display' => false,
            ],
          ],
        ]);

        $servicePieChart = $chartBuilder->createChart(Chart::TYPE_PIE);

        $services = $serviceRepository->findAll();
        $quotes = $quoteRepository->findAll();

        // Filter by current user
        $services = array_filter($services, function ($service) {
          return $service->getServiceCategory()->getAgency() == $this->getUser()->getAgency();
        });
        $quotes = array_filter($quotes, function ($quote) {
          return $quote->getClient()->getAgency() == $this->getUser()->getAgency();
        });

        // make an array with the name of each service and the number of quotes for each service
        $serviceQuotes = array_map(function ($service) use ($quotes) {
          return [
            'name' => $service->getName(),
            'count' => count(array_filter($quotes, function ($quote) use ($service) {
              return in_array($service, $quote->getServices()->toArray());
            })),
          ];
        }, $services);

        // Sort by count descending
        usort($serviceQuotes, function ($a, $b) {
          return $b['count'] - $a['count'];
        });

        // Keep only the top 10
        $serviceQuotes = array_slice($serviceQuotes, 0, 10);

        $servicePieChart->setData([
          'labels' => array_map(function ($serviceQuote) {
            return $serviceQuote['name'];
          }, $serviceQuotes),
          'datasets' => [
            [
              'data' => array_map(function ($serviceQuote) {
                return $serviceQuote['count'];
              }, $serviceQuotes),
            ],
          ],
        ]);

        $servicePieChart->setOptions([
          'maintainAspectRatio' => false,
          'plugins' => [
            'title' => [
              'display' => true,
              'text' => 'Top 10 prestations les plus demandées',
            ],
            'legend' => [
              'display' => true,
              'position' => 'bottom',
            ],
          ],
        ]);

        return $this->render('dashboard/index.html.twig', [
          'turnover' => $turnover,
          'clientCount' => $clientCount,
          'serviceCategoryCount' => $serviceCategoryCount,
          'serviceCount' => $serviceCount,
          'monthlyTurnoverChart' => $monthlyTurnoverChart,
          'servicePieChart' => $servicePieChart,
        ]);
    }
}
