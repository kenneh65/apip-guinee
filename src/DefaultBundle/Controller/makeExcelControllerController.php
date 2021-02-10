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
     * @Route("/{numeroDossier}/{denominationSociale}/{dateCreationDebut}/{dateCreationFin}/{formeJuridique}/statistique-depot-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-depot-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:makeExcelController:stat_depot.html.twig")
     */
    public function statDepotAction($numeroDossier,$denominationSociale,$dateCreationDebut,$dateCreationFin,$formeJuridique)
    {
        $em=$this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $limit=null;
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection
        $data=[
            'numeroDossier'=>$numeroDossier,
            'denominationSociale'=>$denominationSociale,
            'dateCreationDebut'=>$dateCreationDebut,
            'dateCreationFin'=>$dateCreationFin,
            'formeJuridique'=>$formeJuridique,
        ];
        $listerdemande = $em->getRepository('BanquemondialeBundle:DossierDemande')->findDemandeDossierDeposesByParametres($data, 1, $user->getId(), $limit, -1);
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
     * @Route("/{numeroDossier}/{denominationSociale}/{datePaiementDebut}/{datePaiementFin}/{formeJuridique}/{typeDossier}/{entreprise}/{numeroQuittance}/statistique-caisse-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-caisse-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:makeExcelController:stat_caisse.html.twig")
     */
    public function statCaisseAction($numeroDossier,$denominationSociale,$datePaiementDebut,
                                     $datePaiementFin,$formeJuridique,$typeDossier,$entreprise,$numeroQuittance)
    {

        $em=$this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $limit=null;
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection
        $data=[
            'numeroDossier'=>$numeroDossier,
            'denominationSociale'=>$denominationSociale,
            'datePaiementDebut'=>$datePaiementDebut,
            'datePaiementFin'=>$datePaiementFin,
            'formeJuridique'=>$formeJuridique,
            'typeDossier'=>$typeDossier,
            'entreprise'=>$entreprise,
            'numeroQuittance'=>$numeroQuittance,
        ];
        $listQuittance = $em->getRepository('BanquemondialeBundle:Quittance')->findQuittanceValideByParametres($data,1, $user->getId());
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
     * @Route("/{numeroDossier}/{denominationSociale}/{dateCreationDebut}/{dateCreationFin}/{formeJuridique}/statistique-saisie-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-saisie-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:makeExcelController:stat_saisi.html.twig")
     */
    public function statSaisiAction($numeroDossier,$denominationSociale,$dateCreationDebut,$dateCreationFin,$formeJuridique)
    {
        $em=$this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
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
     * @Route("/{numeroDossier}/{denominationSociale}/{dateCreationDebut}/{dateCreationFin}/{dateDelivranceDebut}/{dateDelivranceFin}/{formeJuridique}/{typeDossier}/{entreprise}/{gerant}/statistique-greffe-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-greffe-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:makeExcelController:stat_greffe.html.twig")
     */
    public function statGreffeAction($numeroDossier,$denominationSociale,$dateCreationDebut,$dateCreationFin,$dateDelivranceDebut,$dateDelivranceFin,$formeJuridique,$typeDossier,$entreprise,$gerant)
    {

        $em=$this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $pole = $user->getPole();
        $idPole = 1; //cette valeur est a prendre dans la variable de session à la connection
        $limit=null;
        $data=[
            'numeroDossier'=>$numeroDossier,
            'denominationSociale'=>$denominationSociale,
            'dateCreationDebut'=>$dateCreationDebut,
            'dateCreationFin'=>$dateCreationFin,
            'dateDelivranceDebut'=>$dateDelivranceDebut,
            'dateDelivranceFin'=>$dateDelivranceFin,
            'formeJuridique'=>$formeJuridique,
            'typeDossier'=>$typeDossier,
            'entreprise'=>$entreprise,
            'gerant'=>$gerant,
        ];
        $listerdemande = $em->getRepository('BanquemondialeBundle:DocumentCollected')->findDossierPole($user, $data, 1, $idPole, $limit, 2);
        return [
            'liste'=>$listerdemande,
            'category'=>'Dossiers Délivrés',
            'description'=>'Stat Dossiers Délivrés',
            'user'=>[
                'nom'=>$user->getNom(),
                'prenom'=>$user->getPrenom(),
                'username'=>$user->getUsername()]
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
     * @Route("/{userId}/{service}/{dateDebut}/{dateFin}/statistique-liste-des-dossier-traite-par-agent-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="statistique-liste-des-dossier-traite-par-agent-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:makeExcelController:statistique-liste-des-dossier-traite-par-agent.html.twig")
     */
    public function statDossiersTraiterByUserInExcelAction(Request $request,$userId,$service,$dateDebut,$dateFin)
    {
        $em=$this->getDoctrine()->getManager();
        $stat = $em->getRepository('BanquemondialeBundle:DossierDemande')->getDossiersTraiteByAgentByServiceBydate($userId,$service,$dateDebut,$dateFin);
       $user=$user=$this->get('monservices')->getUserById($userId);
        return [
            'liste'=>$stat,
            'choix'=>$service,
            'user'=>[
                'nom'=>$user->getNom(),
                'prenom'=>$user->getPrenom(),
                'username'=>$user->getUsername()],
            'category'=>'Dossiers retirés',
            'description'=>'Stat Dossiers',
        ];
    }



    /**
     * @Route("/{numeroDossier}/{denominationSociale}/{dateCreationDebut}/{dateCreationFin}/{formeJuridique}/{typeDossier}/{gerant}/{entreprise}/{idS}/{idLangue}/dossierEnmodification-excel.xls", defaults={"_format"="xls"}, requirements={"_format"="csv|xls|xlsx"},name="dossierEnmodification-excel", methods={"GET","POST"})
     * @Template("DefaultBundle:Default:dossierEnmodification-excel.xls.twig")
     */
    public function StatGlobaleCircuitAction()
    {
        return $this->render('DefaultBundle:makeExcelController:stat_globale_circuit.html.twig', array(
            // ...
        ));
    }

}
