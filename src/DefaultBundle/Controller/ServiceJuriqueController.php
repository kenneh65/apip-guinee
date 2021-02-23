<?php

namespace DefaultBundle\Controller;


use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Image;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use DefaultBundle\Entity\ServiceJurique;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * ServiceJurique controller.
 *
 * @Route("/{_locale}/service-juridique")
 */
class ServiceJuriqueController extends Controller
{
    /**
     * Creates a new servicejurique entity.
     *
     * @Route("/verification-conformite-des-statuts-sarl-sarlu", name="verification-conformite-des-statuts-sarl-sarlu")
     * @Security("has_role('ROLE_SERVICE_JURIDIQUE')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $serviceJurique = new ServiceJurique();
        $form = $this->createForm('DefaultBundle\Form\ServiceJuriqueType', $serviceJurique);
        $form->handleRequest($request);
        $user = $this->getUser();

//        $isPiecesIdentite=$request->get('isPiecesIdentite');
//        $isDenomination=$request->get('isDenomination');
//        $isConformiteJuridique=$request->get('isConformiteJuridique');
//        $isCapital=$request->get('isCapital');
//        $isDuree=$request->get('isDuree');
//        $isActivites=$request->get('isActivites');

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
           // die(dump($serviceJurique));
            $em = $this->getDoctrine()->getManager();
            $temoin = $this->verificationExistanceDenomination($em, $request, $serviceJurique->getDenominationCommercial());
            if ($temoin == false) {
                return $this->redirectToRoute('verification-conformite-des-statuts-sarl-sarlu', []);
            }
            $serviceJurique->setIsPiecesIdentite($data['isPiecesIdentite'][0] == 'on' ? 1 : 0)
                ->setIsDenomination($data['isDenomination'][0] == 'on' ? 1 : 0)
                ->setIsConformiteJuridique($data['isConformiteJuridique'][0] == 'on' ? 1 : 0)
                ->setIsCapital($data['isCapital'][0] == 'on' ? 1 : 0)
                ->setIsDuree($data['isDuree'][0] == 'on' ? 1 : 0)
                ->setIsActivites($data['isActivites'][0] == 'on' ? 1 : 0)
                ->setDateVerification(new \DateTime())
                ->setUtilisateur($user);
            $em->persist($serviceJurique);
            $em->flush();
            $this->get('session')->getFlashBag()->add('successSMS', 'Dossier validé avec succès');
            return $this->redirectToRoute('liste-des-dossiers-valider', []);
        }
        return $this->render('DefaultBundle:servicejurique:new-statut-jurirdique.html.twig', array(
            'serviceJurique' => $serviceJurique,
            'form' => $form->createView(),
        ));
    }


    public function verificationExistanceDenomination($em, $request, $denomination){
        $temoin = true;
        $reservation = $em->getRepository('DefaultBundle:reservation')->findOneBy(['statut' => true, 'nomCommercial' => $denomination], []);
        $firstVefication = $this->get('monservices')->verificationNomCommercial($request, $denomination);
        $thirdVerification = $this->get('monservices')->verificationNomCommercialDossierDemande($request, $denomination);
        $fouthVerification = $this->get('monservices')->verificationNomCommercialArchiveNomCommerciale($request, $denomination);
        if ($firstVefication == true || $thirdVerification == true || $fouthVerification == true) {
            $secondVerification = true;
            $message = 'Désolé ce nom commercial est déjà  en utilisation';
            $this->get('session')->getFlashBag()->add('echecSMS', $message);
            $temoin = false;
        } else {
            $secondVerification = $this->get('monservices')->verificationNomCommercialReservation($request, $denomination);
            if ($secondVerification) {
                $message = 'Désolé ce nom commercial est déjà  réserve';
                $this->get('session')->getFlashBag()->add('echecSMS', $message);
                $temoin = false;
            } else {

            }
        }
        return $temoin;
    }

    /**
     * Lists all ServiceJurique entities.
     *
     * @Route("/tableaux-de-bord", name="servicejurique_index")
     * @Security("has_role('ROLE_SERVICE_JURIDIQUE')")
     * @Method("GET")
     */
    public function dashbordAction()
    {
        $em = $this->getDoctrine()->getManager();
        $serviceJuriques = $em->getRepository('DefaultBundle:ServiceJurique')->findAll();
        return $this->render('DefaultBundle:servicejurique:serviceJuridiqueIndex.html.twig', []);
    }


    /**
     * Lists all ServiceJurique entities.
     *
     * @Route("/liste-des-dossiers-valider", name="liste-des-dossiers-valider")
     * @Security("has_role('ROLE_SERVICE_JURIDIQUE')")
     * @Method({"GET", "POST"})
     */
    public function dossierValiderAction(Request $request)
    {
        $denomination=$request->get('nomCommercial');
        $dateDebut=$dateFin=null;
         //   date_format(new \DateTime(),'Y-m-d');
        $em = $this->getDoctrine()->getManager();
        $query=$em->getRepository('DefaultBundle:ServiceJurique')->getDossiersValider($denomination,$dateDebut,$dateFin);
        $paginator  = $this->get('knp_paginator');
        if ($request->getMethod() == 'POST') {
                $dateDebut= $request->get('dateDebut');
                $dateFin=   $request->get('dateFin');
            $query=$em->getRepository('DefaultBundle:ServiceJurique')->getDossiersValider($denomination,$dateDebut,$dateFin);
        }
        $pagination = $paginator->paginate($query,$request->query->getInt('page',1),3);
        return $this->render('DefaultBundle:servicejurique:dossriers-valider.html.twig',
            [
                'serviceJuriques' => $pagination,
                'nomCommercial'=>$denomination,
                'dateDebut'=>($dateDebut),
                'dateFin'=>($dateFin),
            ]);
    }


    public function generateWord()
    {
        $phpWord = new PhpWord();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=test.docx");
        header("Content-Transfer-Encoding: Binary");
        $section = $phpWord->addSection();
        $header = $section->addHeader();
        $header->addImage(
            'new-logo.png',
            array(
                'width' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(3)),
                'height' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(3)),
                'positioning' => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
                'posHorizontal' => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_RIGHT,
                'posVertical' => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
                'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                'posVerticalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                'marginLeft' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(15.5)),
                'marginTop' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(1.55)),
            )
        );
        $header->addImage(
            'embleme.png',
            array(
                'width' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(3)),
                'height' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(3)),
                'positioning' => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
                'posHorizontal' => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_LEFT,
                'posVertical' => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
                'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                'posVerticalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_PAGE,
                'marginRight' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(15.5)),
                'marginTop' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(1.55)),
            )
        );
        // Define styles

        $textrun = $section->addTextRun();
        $textrun->addTextBreak(5);
        $textbox = $section->addTextBox([
            'width' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(5)),
            'height' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(3)),
            'positioning' => \PhpOffice\PhpWord\Style\TextBox::POSITION_RELATIVE,
            'posHorizontal' => \PhpOffice\PhpWord\Style\TextBox::POSITION_HORIZONTAL_RIGHT,
            'marginLeft' => round(\PhpOffice\PhpWord\Shared\Converter::cmToPixel(12)),
        ]);
        $textbox->addText('Monsieur le Directeur Général de AFRILAND FIRST BANK');
        $textrun->addTextBreak(2);
        $textrun->addText('Objet: ');
        $textrun->addText('Ouverture d’un compte bancaire pour la libération du capital social');
//        $section->addImage(
//            'apip.png',
//            array(
//                'width'         => 50,
//                'height'        => 50,
//                'positioning' => Image::POSITION_RELATIVE,
//                'posHorizontal' =>Image::POSITION_RELATIVE,
//                'posVertical' => Image::POSITION_RELATIVE,
//                'marginRight' => round(Converter::cmToPixel(-10)),
//                'wrappingStyle' => 'infront'
//            )
//        );
        $phpWord->save("php://output");

    }

    public function getAndGenerateDocWord($data)
    {
        $phpWord = new PhpWord();
        $fileName = empty($data['denomination']) ? "lettre" : $data['denomination'];
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=$fileName.docx");
        $pathToTemplateFile = 'template.docx';
        $templateProcessor = new TemplateProcessor($pathToTemplateFile);

        $templateProcessor->setValue('denomination', $data['denomination']);
        $templateProcessor->setValue('destinateur', $data['destinateur']);

//        $templateProcessor->setValue('denomination',$data['denomination']);
//        $templateProcessor->setValue('formJuridique', $data['formJuridique']);
//        $templateProcessor->setValue('capital', $data['capital']);
//        $templateProcessor->setValue('siegeSocial', $data['siegeSocial']);
//        $templateProcessor->setValue('activite', $data['activite']);
//        $templateProcessor->setValue('nomSignateur', $data['nomSignateur']);
//        $templateProcessor->setValue('fonctionSignateur', $data['fonctionSignateur']);
//        $templateProcessor->setValue('NumRef', $data['NumRef']);
//        $templateProcessor->setValue('dateJour',$data['dateJour']);
//        $templateProcessor->setValue('destinateur', $data['destinateur']);


        $templateProcessor->saveAs("php://output");

    }
}
