<?php

namespace ParametrageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ParametrageBundle\Form\ArchiveNomCommerciauxType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ParametrageBundle\Entity\ArchiveNomCommerciaux;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * ArchiveNomCommerciaux controller.
 *
 * @Route("/archivenomcommerciaux")
 */
class ArchiveNomCommerciauxController extends Controller {

    /**
     * Lists all ArchiveNomCommerciaux entities.
     *
     * @Route("/", name="archivenomcommerciaux_index")
     * @Method("GET")
     */
    public function indexAction() {

        return $this->render('ParametrageBundle:ArchiveNomCommerciaux:index.html.twig');
    }

    /**
     * @Route("/nom-commerciaux", name="nom_commerciaux_pagination") 
     */
    public function paginateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $codLang = $request->getLocale();
        $langue = $em->getRepository('BanquemondialeBundle:Langue')->findOneByCode($codLang);
        $idLangue = $langue->getId();

        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $length = $request->get('length');
            $length = $length && ($length != -1) ? $length : 0;

            $start = $request->get('start');
            $start = $length ? ($start && ($start != -1) ? $start : 0) / $length : 0;

            $search = $request->get('search');
            $filters = [
                'query' => @$search['value']
            ];



            $archiveNomCommerciaux = $this->getDoctrine()->getRepository('ParametrageBundle:ArchiveNomCommerciaux')->search($filters, $start, $length);
            $nouveauxNomCommerciaux = $this->getDoctrine()->getRepository('BanquemondialeBundle:DocumentCollected')->search($filters, $start, $length, true, $idLangue);

            $output = array(
                'data' => array(),
                'recordsFiltered' => 0,
                'recordsTotal' => 0
            );

            foreach ($archiveNomCommerciaux as $nomCommercial) {
                $output['data'][] = [
                    'denominationSociale' => $nomCommercial->getDenominationSociale(),
                    'formeJuridique' => $nomCommercial->getFormeJuridique(),
                    'rccm' => $nomCommercial->getRccm(),
                    'gerantPrincipal' => $nomCommercial->getGerantPrincipal(),
                    'secteurActivite' => $nomCommercial->getSecteurActivite(),
                    'telephone' => $nomCommercial->getTelephone(),
                    'siegeSocial' => $nomCommercial->getSiegeSocial(),
                    'anneeCreation' => $nomCommercial->getAnneeCreation(),
                ];
            }

            $output2 = array(
                'data' => array(),
                'recordsFiltered' => 0,
                'recordsTotal' => 0
            );


            foreach ($nouveauxNomCommerciaux as $nouveauNomCommercial) {
                $output2['data'][] = [
                    'denominationSociale' => $nouveauNomCommercial["denominationSociale"],
                    'formeJuridique' => $nouveauNomCommercial["libelleFormeJuridique"],
                    'rccm' => $nouveauNomCommercial["numRccmFormalite"],
                    'gerantPrincipal' => $nouveauNomCommercial["gerant"],
                    'secteurActivite' => $nouveauNomCommercial["libelleSecteurActivite"],
                    'telephone' => $nouveauNomCommercial["telephoneEntreprise"],
                    'siegeSocial' => $nouveauNomCommercial["adresseSiege"],
                    'anneeCreation' => substr($nouveauNomCommercial["dateRccm"], 8, 2) . "-" . substr($nouveauNomCommercial["dateRccm"], 5, 2) . "-" . substr($nouveauNomCommercial["dateRccm"], 0, 4),
                ];
            }

            $output3['data'] = array_merge($output2['data'], $output['data']);
            //die(dump($output3['data']));
            $output3['recordsFiltered'] = count($this->getDoctrine()->getRepository('BanquemondialeBundle:DocumentCollected')->search($filters, 0, false, true, $idLangue)) + count($this->getDoctrine()->getRepository('ParametrageBundle:ArchiveNomCommerciaux')->search($filters, 0, false, true, $idLangue));
            $output3['recordsTotal'] = count($this->getDoctrine()->getRepository('BanquemondialeBundle:DocumentCollected')->search(array(), 0, false, true, $idLangue)) + count($this->getDoctrine()->getRepository('ParametrageBundle:ArchiveNomCommerciaux')->search(array(), 0, false, true, $idLangue));

            return new Response(json_encode($output3), 200, ['Content-Type' => 'application/json']);
        } else
            return new Response();
    }

    /**
     * @Route("/archive-nom-commerciaux", name="archive_nom_commerciaux_pagination") 
     */
    public function paginateArchiveAction(Request $request) {

        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $length = $request->get('length');
            $length = $length && ($length != -1) ? $length : 0;

            $start = $request->get('start');
            $start = $length ? ($start && ($start != -1) ? $start : 0) / $length : 0;

            $search = $request->get('search');
            $filters = [
                'query' => @$search['value']
            ];

            $archiveNomCommerciaux = $this->getDoctrine()->getRepository('ParametrageBundle:ArchiveNomCommerciaux')->search($filters, $start, $length);

            $output = array(
                'data' => array(),
                'recordsFiltered' => count($this->getDoctrine()->getRepository('ParametrageBundle:ArchiveNomCommerciaux')->search($filters, 0, false)),
                'recordsTotal' => count($this->getDoctrine()->getRepository('ParametrageBundle:ArchiveNomCommerciaux')->search(array(), 0, false))
            );

            foreach ($archiveNomCommerciaux as $nomCommercial) {
                $output['data'][] = [
                    'denominationSociale' => $nomCommercial->getDenominationSociale(),
                    'formeJuridique' => $nomCommercial->getFormeJuridique(),
                    'rccm' => $nomCommercial->getRccm(),
                    'gerantPrincipal' => $nomCommercial->getGerantPrincipal(),
                    'secteurActivite' => $nomCommercial->getSecteurActivite(),
                    'telephone' => $nomCommercial->getTelephone(),
                    'siegeSocial' => $nomCommercial->getSiegeSocial(),
                    'anneeCreation' => $nomCommercial->getAnneeCreation(),
                ];
            }

            return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
        } else
            return new Response();
    }

}
