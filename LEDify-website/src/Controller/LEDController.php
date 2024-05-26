<?php

namespace App\Controller;

use App\Entity\Display;
use App\Entity\Led;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LEDController extends AbstractController
{
    private $adresseIP;
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->adresseIP = '192.168.1.49';
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/control-led', name: 'control_led', methods: ['GET','POST'])]
    public function controlLed(EntityManagerInterface $entityManager, Request $request): Response
    {
        $action = $request->request->get('action');

        if ($action !== 'on' && $action !== 'off') {
            $this->addFlash('error', 'Action invalide');
        } else {
            $user = $this->getUser();
            if (!$user) {
                $this->addFlash('error', 'Utilisateur non connecté');
                return $this->redirectToRoute('login');
            }
            $response = $this->httpClient->request('GET', 'http://' . $this->adresseIP . '/led', [
                'query' => [
                    'action' => $action,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                // Créer un objet DateTime pour représenter la date actuelle
                $dateTime = new \DateTime();
                // Enregistrer l'état de la LED en base de données
                $led = new Led();
                $led->setUser($user);
                // Définir l'état de la LED en fonction de l'action
                $led->setEtat($action === 'on' ? 1 : 0);
                $led->setDate($dateTime);
                $entityManager->persist($led);
                $entityManager->flush();

                $this->addFlash('success', 'Commande envoyée avec succès');
            } else {
                $this->addFlash('error', 'Erreur lors de l\'envoi de la commande à l\'ESP32');
            }
        }

        return $this->redirectToRoute('home');
    }

    public function interrupteur(EntityManagerInterface $entityManager, Request $request): Response
    {
        $ledState = $entityManager->getRepository(Led::class)->findOneBy(['user' => $this->getUser()],['date' => 'DESC']);

        return $this->render('components/interrupteur.html.twig', [
            'ledState' => $ledState,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/display_word_and_color', name: 'display_word_and_color', methods: ['GET','POST'])]
    public function displayWordAndColor(EntityManagerInterface $entityManager, Request $request): Response
    {
        $word = $request->request->get('word');
        $color = $request->request->get('color');

        if (!$word) {
            $this->addFlash('error', 'Le mot à afficher est manquant.');
        } else {
            $user = $this->getUser();
            if (!$user) {
                $this->addFlash('error', 'Utilisateur non connecté');
                return $this->redirectToRoute('login'); // Rediriger vers la page de connexion
            }

            // Envoyer le mot et la couleur à l'ESP32
            $response = $this->httpClient->request('GET', 'http://' . $this->adresseIP . '/display_word_and_color', [
                'query' => [
                    'word' => $word,
                    'color' => $color,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $dateTime = new \DateTime();
                // Enregistrer les détails dans la base de données
                $display = new Display();
                $display->setUser($user);
                $display->setText($word);
                $display->setColor($color);
                $display->setDate($dateTime);
                $entityManager->persist($display);
                $entityManager->flush();

                $this->addFlash('success', 'Mot affiché avec succès sur les matrices de LED');
            } else {
                $this->addFlash('error', 'Erreur lors de l\'envoi du mot à afficher à l\'ESP32');
            }

            return $this->redirectToRoute('home');
        }

        return $this->render('pages/home.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/color', name: 'color', methods: ['GET', 'POST'])]
    public function chooseColor(Request $request): Response
    {
        $color = $request->request->get('color');

        $response = $this->httpClient->request('GET', 'http://' . $this->adresseIP . '/color', [

            'query' => [
                'color' => $color,
            ],
        ]);

        if ($response->getStatusCode() === 200) {
            $this->addFlash('success', 'Couleur des LED mise à jour avec succès');
        } else {
            $this->addFlash('error', 'Erreur lors de la mise à jour de la couleur des LED');
        }

        return $this->render('pages/home.html.twig', []);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/mode', name: 'mode', methods: ['GET', 'POST'])]
    public function chooseMode(Request $request): Response
    {
        $mode = $request->request->get('option');

//        $mode = $request->query->getInt('mode'); // Utiliser query au lieu de request pour récupérer les paramètres GET

        $response = $this->httpClient->request('GET', 'http://' . $this->adresseIP . '/mode', [
            'query' => [
                'mode' => $mode,
            ],
        ]);

        if ($response->getStatusCode() === 200) {
            $this->addFlash('success', 'Mode envoyé avec succès');
        } else {
            $this->addFlash('error', 'Erreur lors de l\'envoi du mode à l\'Arduino');
        }

        return $this->redirectToRoute('home');
    }

}