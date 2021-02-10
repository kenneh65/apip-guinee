<?php

namespace BanquemondialeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * RccmRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RccmRepository extends EntityRepository
{
    public function verifierNumeroFormalite($numRccmFormalite, $idd)
    {
        $db = $this->createQueryBuilder('r')
            ->where('r.numRccmFormalite = :numRccmFormalite')
            ->andWhere('substring(r.numRccmFormalite,8,4) = substring(:numRccmFormalite,8,4)')
            ->andWhere('r.dossierDemande != :idd')
            ->setParameters(array('numRccmFormalite' => $numRccmFormalite, 'idd' => $idd));
        return $db->getQuery()->getResult();
    }

    public function verifierNumeroEntreprise($numRccmEntreprise, $idd)
    {
        $db = $this->createQueryBuilder('r')
            ->where('r.numRccmEntreprise = :numRccmEntreprise')
            ->andWhere('substring(r.numRccmEntreprise,8,4) = substring(:numRccmEntreprise,8,4)')
            ->andWhere('r.dossierDemande != :idd')
            ->setParameters(array('numRccmEntreprise' => $numRccmEntreprise, 'idd' => $idd));
        return $db->getQuery()->getResult();
    }

    public function findDossierByRccmAndNumeroDossier($numeroDossier, $numRccmEntreprise)
    {

        $db = $this->createQueryBuilder('r')
            ->join('r.dossierDemande', 'd')
            ->where('d.numeroDossier = :numeroDossier')
            ->andWhere('r.numRccmEntreprise = :numRccmEntreprise')
            ->setParameters(array('numeroDossier' => $numeroDossier, 'numRccmEntreprise' => $numRccmEntreprise));
        return $db->getQuery()->getResult();
    }

    public function findDossierValideById($idd)
    {
        $db = $this->createQueryBuilder('r')
            ->join('r.dossierDemande', 'd')
            ->where('d.id = :idd')
            ->andWhere('d.statutValidationChefGreffe = 2')
            ->setParameters(array('idd' => $idd));
        return $db->getQuery()->getOneOrNullResult();
    }


    public function getRccmByPeriode($dateDebut, $dateFin)
    {
        $em = $this->getEntityManager()->getConnection();
        if (empty($dateDebut)) {
            $dateDebut = date_format(new \DateTime(), 'Y-m-d');
        }
        if (empty($dateFin)) {
            $dateFin = date_format(new \DateTime(), 'Y-m-d');
        }

        $sql = "SELECT ft.libelle, f.sigle, d.id numeroDossier, d.denominationSociale,d.dateCreation, r.* FROM rccm r 
INNER JOIN dossierdemande d ON d.id=r.idDossierDemande  INNER JOIN formejuridique f ON f.id=d.idFormeJuridique INNER JOIN 
    formejuridiquetraduction ft ON ft.idformeJuridique=f.id AND ft.idLangue=1
  WHERE   (CAST( r.date AS DATE )  BETWEEN  cast('$dateDebut' AS DATE) AND  cast('$dateFin' AS DATE)) order by d.denominationSociale asc ";


        try {
            $statement = $em->prepare($sql);
            //die(dump($statement));
        } catch (DBALException $e) {

        }
        $statement->execute();
        $rccm = $statement->fetchAll();
        return $rccm;
    }

    public function getRccmByPeriodeBis($dateDebut = "", $dateFin = "",$nomCommercial=null)
    {
        $em = $this->getEntityManager()->getConnection();
        if (!empty($nomCommercial)) {
            $param ="\"%$nomCommercial%\"";
            $nomCommercial = " AND d.denominationSociale LIKE ".$param;
        }
        if (!empty($dateDebut)) {
            $dateDebut = " AND  CAST( r.date AS DATE ) >=CAST('$dateDebut' AS DATE) ";
        }
        if (!empty($dateFin)) {
            $dateFin = " AND  CAST( r.date AS DATE ) <=CAST('$dateFin' AS DATE) ";
        }

        $sql = "SELECT ft.libelle, f.sigle, d.id numeroDossier, d.denominationSociale,d.dateCreation, r.* FROM rccm r 
            INNER JOIN dossierdemande d ON d.id=r.idDossierDemande  INNER JOIN formejuridique f ON f.id=d.idFormeJuridique INNER JOIN 
    formejuridiquetraduction ft ON ft.idformeJuridique=f.id AND ft.idLangue=1 AND r.statut IS NULL
  " . $dateDebut . $dateFin .  $nomCommercial." order by d.denominationSociale asc ";

        try {
            $statement = $em->prepare($sql);
           // die(dump($statement));
        } catch (DBALException $e) {

        }
        $statement->execute();

        $rccm = $statement->fetchAll();
      // die(dump($rccm));
        return $rccm;
    }

    public function getRccmTraiter($dateDebut, $dateFin)
    {
        $slqRequete = "SELECT r.* FROM rccm r  
INNER JOIN dossierdemande d ON d.id=r.idDossierDemande
INNER JOIN formejuridique f ON f.id=d.idFormeJuridique
INNER JOIN formejuridiquetraduction ft ON ft.idformeJuridique=f.id 
AND ft.idLangue=1
AND CAST(r.date AS DATE) BETWEEN CAST('$dateDebut' AS DATE ) AND  CAST('$dateFin' AS DATE )
ORDER BY d.denominationSociale ASC ,r.date DESC ";
        $stmt = $this->getEntityManager()->getConnection()->prepare($slqRequete);
        $stmt->execute();
        $results = $stmt->fetchAll();
        return $results;
    }

    public function getRccmNonTraiter($dateDebut, $dateFin)
    {
        $slqRequete = "SELECT Tab.* FROM (
SELECT e.denomination 'structure',d.* FROM dossierdemande d
 INNER JOIN formejuridique f ON f.id=d.idFormeJuridique
 INNER JOIN formejuridiquetraduction ft ON ft.idformeJuridique=f.id
 INNER JOIN fos_user u ON u.id=d.idUserDepot
 INNER JOIN entreprise e ON e.id=u.entreprise_id
 INNER JOIN quittance q ON q.idDossierDemande=d.id
 AND q.isPaid =true
 AND ft.idLangue=1
 AND d.idUtilisateur IS NOT  NULL
 AND d.statut =1
 AND d.activiteSociale IS NULL
 AND CAST(q.dateFacturation AS DATE) BETWEEN CAST('$dateDebut' AS DATE ) AND  CAST('$dateFin' AS DATE )

 AND d.id IN
 -- non encore saisie
 (
 -- ' Liste des dossiers non encore saisie'
SELECT d.id FROM dossierdemande d
 INNER JOIN formejuridique f ON f.id=d.idFormeJuridique
 INNER JOIN formejuridiquetraduction ft ON ft.idformeJuridique=f.id
 INNER JOIN fos_user u ON u.id=d.idUserDepot
 INNER JOIN entreprise e ON e.id=u.entreprise_id
 INNER JOIN quittance q ON q.idDossierDemande=d.id
 AND q.isPaid =true
 AND ft.idLangue=1
 AND d.idUtilisateur IS NOT  NULL
 AND q.idDossierDemande  IN  (
 -- Quittancer
SELECT  q.idDossierDemande FROM  quittance q
 INNER JOIN dossierdemande d ON d.id=q.idDossierDemande
 INNER JOIN formejuridique f ON f.id=d.idFormeJuridique
 INNER JOIN formejuridiquetraduction ft ON ft.idformeJuridique=f.id
 INNER JOIN modepaiement m ON m.id=q.idModePaiement
 INNER JOIN modepaiementtraduction mt ON mt.idModePaiement=m.id
 INNER JOIN fos_user u ON u.id=q.idUtilisateur
 INNER JOIN entreprise e ON e.id=u.entreprise_id
 AND mt.idLangue=1
 AND ft.idLangue=1
 AND q.isPaid=1
AND CAST(q.datePaiement AS DATE) BETWEEN CAST('$dateDebut' AS DATE ) AND  CAST('$dateFin' AS DATE )
ORDER BY q.denominationSociale ASC ,q.datePaiement DESC
 )
AND CAST(q.dateFacturation AS DATE) BETWEEN CAST('$dateDebut' AS DATE ) AND  CAST('$dateFin' AS DATE )
ORDER BY q.denominationSociale ASC ,q.dateFacturation DESC
 )
 -- Ende Non encore saisie
ORDER BY q.denominationSociale ASC ,q.dateFacturation DESC
) AS Tab WHERE tab.id NOT IN (SELECT r.idDossierDemande FROM rccm r )
union
(
SELECT Tab.* FROM (
SELECT e.denomination 'structure',d.* FROM dossierdemande d
 INNER JOIN formejuridique f ON f.id=d.idFormeJuridique
 INNER JOIN formejuridiquetraduction ft ON ft.idformeJuridique=f.id
 INNER JOIN fos_user u ON u.id=d.idUserDepot
 INNER JOIN entreprise e ON e.id=u.entreprise_id
 INNER JOIN quittance q ON q.idDossierDemande=d.id
 AND q.isPaid =true
 AND ft.idLangue=1
 AND d.idUtilisateur IS NOT  NULL
 AND d.statut =3
 AND d.activiteSociale IS NULL
 AND CAST(q.dateFacturation AS DATE) BETWEEN CAST('$dateDebut' AS DATE ) AND  CAST('$dateFin' AS DATE )

 AND d.id IN
 -- non encore saisie
 (
 -- ' Liste des dossiers non encore saisie'
SELECT d.id FROM dossierdemande d
 INNER JOIN formejuridique f ON f.id=d.idFormeJuridique
 INNER JOIN formejuridiquetraduction ft ON ft.idformeJuridique=f.id
 INNER JOIN fos_user u ON u.id=d.idUserDepot
 INNER JOIN entreprise e ON e.id=u.entreprise_id
 INNER JOIN quittance q ON q.idDossierDemande=d.id
 AND q.isPaid =true
 AND ft.idLangue=1
 AND d.idUtilisateur IS NOT  NULL
 AND q.idDossierDemande  IN  (
 -- Quittancer
SELECT  q.idDossierDemande FROM  quittance q
 INNER JOIN dossierdemande d ON d.id=q.idDossierDemande
 INNER JOIN formejuridique f ON f.id=d.idFormeJuridique
 INNER JOIN formejuridiquetraduction ft ON ft.idformeJuridique=f.id
 INNER JOIN modepaiement m ON m.id=q.idModePaiement
 INNER JOIN modepaiementtraduction mt ON mt.idModePaiement=m.id
 INNER JOIN fos_user u ON u.id=q.idUtilisateur
 INNER JOIN entreprise e ON e.id=u.entreprise_id
 AND mt.idLangue=1
 AND ft.idLangue=1
 AND q.isPaid=1
AND CAST(q.datePaiement AS DATE) BETWEEN CAST('$dateDebut' AS DATE ) AND  CAST('$dateFin' AS DATE )
ORDER BY q.denominationSociale ASC ,q.datePaiement DESC
 )
AND CAST(q.dateFacturation AS DATE) BETWEEN CAST('$dateDebut' AS DATE ) AND  CAST('$dateFin' AS DATE )
ORDER BY q.denominationSociale ASC ,q.dateFacturation DESC
 )
 -- Ende Non encore saisie
ORDER BY q.denominationSociale ASC ,q.dateFacturation DESC
) AS Tab WHERE tab.id NOT IN (SELECT r.idDossierDemande FROM rccm r )
)
";
        $stmt = $this->getEntityManager()->getConnection()->prepare($slqRequete);
        $stmt->execute();
        $results = $stmt->fetchAll();
        return $results;
    }
}
