<?php
namespace DefaultBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class makeExcelControllerController extends Controller
{
    /**
     * @Route("/{dateCreationDebut}/{dateCreationFin}/{userid}/statistique-depot-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-depot-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:makeExcelController:stat_depot.html.twig")
     */
    public function statDepotAction($dateCreationDebut,$dateCreationFin,$userid)
    {
        $em=$this->getDoctrine()->getManager();
        $user = $this->get('monservices')->getUserById($userid);
        $limit=null;
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection
        $data=[
            'dateCreationDebut'=>date_format(new \DateTime($dateCreationDebut),'Y-m-d'),
            'dateCreationFin'=>date_format(new \DateTime($dateCreationFin),'Y-m-d')
        ];
        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDossierDeposerByAgentByPeriod($data['dateCreationDebut'],$data['dateCreationFin'], $user->getId());
        return [
            'liste'=>$listerdemande,
            'category'=>'Dossiers déposés',
            'description'=>'Stat Dossiers déposés',
            'user'=>[
                'nom'=>$user->getNom(),
                'prenom'=>$user->getPrenom(),
                'username'=>$user->getUsername()]
        ];
    }

    /**
     * @Route("/{datePaiementDebut}/{datePaiementFin}/{userid}/statistique-caisse-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-caisse-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:makeExcelController:stat_caisse.html.twig")
     */
    public function statCaisseAction($datePaiementDebut,$datePaiementFin,$userid)
    {

        $em=$this->getDoctrine()->getManager();
        $user = $this->get('monservices')->getUserById($userid);
        $limit=null;
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection
        $data=[
            'datePaiementDebut'=>date_format(new \DateTime($datePaiementDebut),'Y-m-d'),
            'datePaiementFin'=>date_format(new \DateTime($datePaiementFin),'Y-m-d')
        ];
        $listQuittance = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceTraiterByAgentByPeriod($data['datePaiementDebut'],$data['datePaiementFin'],$user->getId());
        return [
            'listQuittance'=>$listQuittance,
            'category'=>'Liste des quittances',
            'description'=>'Stat Liste des quittances',
            'user'=>[
                'nom'=>$user->getNom(),
                'prenom'=>$user->getPrenom(),
                'username'=>$user->getUsername()]
        ];
    }

    /**
     * @Route("/{numeroDossier}/{denominationSociale}/{dateCreationDebut}/{dateCreationFin}/{formeJuridique}/{userid}/statistique-saisie-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-saisie-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:makeExcelController:stat_saisi.html.twig")
     */
    public function statSaisiAction($numeroDossier,$denominationSociale,$dateCreationDebut,$dateCreationFin,$formeJuridique,$userid)
    {
        $em=$this->getDoctrine()->getManager();
        $user = $this->get('monservices')->getUserById($userid);
        $limit=null;
        $data=[
            'numeroDossier'=>$numeroDossier,
            'denominationSociale'=>$denominationSociale,
            'dateCreationDebut'=>$dateCreationDebut,
            'dateCreationFin'=>$dateCreationFin,
            'formeJuridique'=>$formeJuridique,
        ];
        $listDossier = $em->getRepository('BanquemondialeBundle:DossierDemande')->findStatistiqueDossierSaisieBy($data,$user->getId());
        return [
            'liste'=>$listDossier,
            'category'=>'Dossiers saisi',
            'description'=>'Stat Dossiers saisi',
            'user'=>[
                'nom'=>$user->getNom(),
                'prenom'=>$user->getPrenom(),
                'username'=>$user->getUsername()]
        ];
    }

    /**
     * @Route("/{dateCreationDebut}/{dateCreationFin}/{userid}/statistique-greffe-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-greffe-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:makeExcelController:stat_greffe.html.twig")
     */
    public function statGreffeAction($dateCreationDebut,$dateCreationFin,$userid)
    {

        $em=$this->getDoctrine()->getManager();
        $user = $this->get('monservices')->getUserById($userid);
        $limit=null;
        $data=[
            'dateCreationDebut'=>date_format(new \DateTime($dateCreationDebut),'Y-m-d'),
            'dateCreationFin'=>date_format(new \DateTime($dateCreationFin),'Y-m-d')
        ];
        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->getRapportRccmTraiterByPeriode($data['dateCreationDebut'],$data['dateCreationFin']);
        return [
            'liste'=>$listerdemande,
            'category'=>'Dossiers Délivrés',
            'description'=>'Stat Dossiers Délivrés',
            'user'=>[
                'nom'=>!empty($user)?$user->getNom():"",
                'prenom'=>!empty($user)?$user->getPrenom():"",
                'username'=>!empty($user)?$user->getUsername():""]
        ];
    }

    /**
     * @Route("/{numeroDossier}/{denominationSociale}/{dateCreationDebut}/{dateCreationFin}/{formeJuridique}/{typeDossier}/{entreprise}/{gerant}/statistique-retait-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-retait-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:makeExcelController:stat_retrait.html.twig")
     */
    public function statRetraitAction($numeroDossier,$denominationSociale,$dateCreationDebut,$dateCreationFin,$formeJuridique,$typeDossier,$entreprise,$gerant)
    {
        $em=$this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $idS = $user->getEntreprise()->getId();
        $pole = $user->getPole();
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection
        $limit=null;
        $data=[
            'numeroDossier'=>$numeroDossier,
            'denominationSociale'=>$denominationSociale,
            'dateCreationDebut'=>$dateCreationDebut,
            'dateCreationFin'=>$dateCreationFin,
            'formeJuridique'=>$formeJuridique,
            'typeDossier'=>$typeDossier,
            'entreprise'=>$entreprise,
            'gerant'=>$gerant,
        ];
        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPoleRetire($user, $data, 1, $idPole, null, 2, $idS);
        return [
            'liste'=>$listerdemande,
            'category'=>'Dossiers retirés',
            'description'=>'Stat Dossiers retirés',
            'user'=>[
                'nom'=>$user->getNom(),
                'prenom'=>$user->getPrenom(),
                'username'=>$user->getUsername()]
        ];
    }

    /**
     * @Route("/{dateCreationDebut}/{dateCreationFin}/statistique-dossier-retirer-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-dossier-retirer-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:makeExcelController:statistique-liste-des-dossier-traite-par-agent.html.twig")
     */
    public function statDossiersTraiterByUserInExcelAction(Request $request ,$dateCreationDebut,$dateCreationFin)
    {
        $em=$this->getDoctrine()->getManager();
        $user = $this->get('monservices')->getUserById(326);
        $data=[
            'dateCreationDebut'=>date_format(new \DateTime($dateCreationDebut),'Y-m-d'),
            'dateCreationFin'=>date_format(new \DateTime($dateCreationFin),'Y-m-d')
        ];
        $stat = $em->getRepository('BanquemondialeBundle:DossierDemande')->getStatistiqueDossierRetirerByAgentPeriode($data['dateCreationDebut'],$data['dateCreationFin']);
      // die(dump($stat));
        return [
            'liste'=>$stat,
            'user'=>[
                'nom'=>$user->getNom(),
                'prenom'=>$user->getPrenom(),
                'username'=>$user->getUsername()],
            'category'=>'Dossiers retirés',
            'description'=>'Stat des Dossiers retirés',
        ];
    }
//    /**
//     * @Route("/{numeroDossier}/{denominationSociale}/{dateCreationDebut}/{dateCreationFin}/{formeJuridique}/{typeDossier}/{gerant}/{entreprise}/{idS}/{idLangue}/dossierEnmodification-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="dossierEnmodification-excel", methods={"GET","POST"})
//     * @Template("DefaultBundle:Default:dossierEnmodification-excel.xls.twig")
//     */
//    public function StatGlobaleCircuitAction()
//    {
//        return $this->render('DefaultBundle:makeExcelController:stat_globale_circuit.html.twig', array(
//            // ...
//        ));
//    }

        /**
     * @Route("/{dateDebut}/{dateFin}/statistique-generale-dossier-en-circuit-excel-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-generale-dossier-en-circuit-excel-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:statistiqueDossiers:statistique-generale-dossier-en-circuit-excel.html.twig")
     */
    public function StatistiqueGeneraleCircuitDossierByPeriodeExcelAction(Request $request,$dateDebut,$dateFin)
    {
        $em=$this->getDoctrine()->getManager();
        $user = $this->get('monservices')->getUserById(326);
        $dDebut = date_format(new \DateTime($dateDebut), 'Y-m-d');
        $dFin = date_format(new \DateTime($dateFin), 'Y-m-d');
        $statGenerale = $this->get('suivistatutdossierservice')->getStatistiqueGeneralCircuitDossier($dDebut, $dFin);
        return
            [
                'statGenerale' => $statGenerale,
                'dateDebut' => $dateDebut,
                'dateFin' => $dateFin,
                'user'=>[
                    'nom'=>$user->getNom(),
                    'prenom'=>$user->getPrenom(),
                    'username'=>$user->getUsername()],
                'category'=>'Dossiers retirés',
                'description'=>'Stat des Dossiers retirés',
            ];
    }

}
