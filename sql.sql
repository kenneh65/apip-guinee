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