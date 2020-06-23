<?php
namespace UtilisateursBundle\Controller;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use BanquemondialeBundle\Entity\Administration;
use UtilisateursBundle\Entity\Utilisateurs;

class LoadUser implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {	
		$utilisateur = new Utilisateurs();
			$utilisateur->setEnabled(true);
			$utilisateur->setFirstLog(false);	
			$utilisateur->setNom('Admin');	
			$utilisateur->setPrenom('Admin');
			$utilisateur->setType('Administration');
			$utilisateur->setGenre('m');
			$utilisateur->setCni('12323221232');
			$utilisateur->setDateNaissance(new \DateTime());
			$utilisateur->setLieuNaissance('dakar');
			$utilisateur->setPaysResidence(null);
			$utilisateur->setRegionResidence(null);
			$utilisateur->setDepartement(null);
			$utilisateur->setAdresse('Dakar');
			$utilisateur->setTelephone('776788025');
			$utilisateur->setRoles(array('ROLE_SUPER_ADMIN'));
			$utilisateur->setUsername('admin');
			$utilisateur->setPlainPassword('Admin_OBE_2');
			$utilisateur->setEmail('admin@mbd.com');
			
		$administration = new Administration();
			$administration->setProfil(null);		
			$administration->setUtilisateur($utilisateur);
			$administration->setDenomination('Admin');	
		
		$utilisateur->setAdministration($administration);
		$utilisateur->setParticulier(null);
		$utilisateur->setEntreprise(null);
					
		$manager->persist($utilisateur);
		$manager->persist($administration);
		
        $manager->flush();
    }
}