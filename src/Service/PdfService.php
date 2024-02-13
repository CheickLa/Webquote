<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PdfService
{
    private $domPdf;
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->domPdf = new DomPdf();

        $pdfOptions = new Options();

        $pdfOptions->set('defaultFont', 'Garamond');

        $this->domPdf->setPaper('A4','landscape');

        $pdfOptions->setIsPhpEnabled(true);

        $pdfOptions->setIsRemoteEnabled(true);

        $this->domPdf->setOptions($pdfOptions);

        $this->twig = $twig;
    }

    // Fonction qui permet de générer le pdf sans l'afficher à l'utilisateur
    // Idéale pour l'attacher à des mails par la suite
    public function generatePdf($html,$fileName)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->setPaper('A4','landscape');
        $this->domPdf->render();
        $output = $this->domPdf->output();
        if (!is_dir('pdf')) {
          mkdir('pdf');
        }
        file_put_contents('pdf/'.$fileName.'.pdf',$output);
    }

}
