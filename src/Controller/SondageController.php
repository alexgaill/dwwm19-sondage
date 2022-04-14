<?php

namespace App\Controller;

use App\Entity\Sondage;
use App\Form\SondageType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SondageController extends AbstractController
{
    #[Route('/', name:'home')]
    public function home(ManagerRegistry $manager): Response
    {
        $sondages = $manager->getRepository(Sondage::class)->findAll();
        
        foreach ($sondages as $sondage) {
            foreach ($sondage->getQuestions() as $question) {
                $highScore = 0;
                // Je parcours les réponses de chaque question et pour chaque question j'enregistre le score le plus élevé
                foreach ($question->getReponses() as $reponse) {
                    $reponse->getScore() > $highScore ? $highScore = $reponse->getScore() : null;
                }
                // Je parcours à nouveau mes réponses et si le score de la réponse est égal au highScore, je passe la propriété highScore à true
                foreach ($question->getReponses() as $reponse) {
                    $reponse->getScore() == $highScore ? $reponse->setHighScore(true) : null;
                }
            }
        }

        return $this->render('sondage/home.html.twig', [
            'sondages' => $sondages
        ]);
    }

    #[Route('/sondage/save', name:"save_sondage", methods: ['GET', 'POST'])]
    public function save (Request $request, ManagerRegistry $manager): Response
    {
        $sondage = new Sondage;
        $form = $this->createForm(SondageType::class, $sondage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $om = $manager->getManager();
            $om->persist($sondage);
            $om->flush();
            return $this->redirectToRoute('single_sondage', ['id' => $sondage->getId()]); 
        }

        return $this->renderForm('sondage/save.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/sondage/{id}', name:'single_sondage', methods:'GET', requirements: ['id' => '\d+'])]
    public function single (int $id, ManagerRegistry $manager): Response
    {
        $sondage = $manager->getRepository(Sondage::class)->find($id);
        if ($sondage) {
            return $this->render('sondage/single.html.twig', [
                'sondage' => $sondage
            ]);
        }
        return $this->redirectToRoute('home');
    }
}
