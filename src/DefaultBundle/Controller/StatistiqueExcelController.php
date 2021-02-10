<?php

namespace DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
class StatistiqueExcelController extends Controller
{


    /**
     * @Route("liste-dossiers-quittancer-or-not-by-pariode-en-pdf")
     */
    public function dossierQuittancerOrNotByPeriodePdfAction()
    {
        return $this->render('DefaultBundle:StatistiquePdf:dossier_quittancer_or_not_by_periode_pdf.html.twig', array(
            // ...
        ));
    }

}
