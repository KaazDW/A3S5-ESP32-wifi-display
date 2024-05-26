<?php

namespace App\Controller;

use App\Entity\Display;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/home', name: 'home')]
    public function index(Request $request): Response
    {
//        if(!$this->getUser()){
//            return $this->redirectToRoute('app_login');
//        }
        return $this->render('pages/home.html.twig', [

        ]);
    }

    #[Route('/historique', name: 'historique')]
    public function historiqueText(EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        if ($user instanceof User) {
            $userId = $user->getId();
            // Récupérer les mots de l'utilisateur connecté depuis la base de données
            $displayRepository = $entityManager->getRepository(Display::class);
            $userWords = $displayRepository->findBy(['user' => $userId],['date'=>'DESC'],11);


            return $this->render('components/historique.html.twig', [
                'userWords' => $userWords,
            ]);
        }

        // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
        return $this->redirectToRoute('app_login');
    }
}