<?php

namespace DefaultBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StatistiqueExelController extends Controller
{
    /**
     * @Route("/{datedebut}/{datefin}/liste-dossiers-deposer-by-periode-en-exel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="liste-dossiers-deposer-by-periode-en-exel", methods={"GET","POST"})
     * @Template("DefaultBundle:StatistiqueExcel:dossier_deposer_by_periode_pdf.html.twig")
     */
    public function dossierDeposerByPeriodeExelAction($datedebut, $datefin)
    {
        return $this->render('DefaultBundle:StatistiquePdf:dossier_deposer_by_periode_pdf.html.twig', array());
    }

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
