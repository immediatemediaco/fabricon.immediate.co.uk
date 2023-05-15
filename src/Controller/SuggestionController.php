<?php

namespace App\Controller;

use App\Entity\Suggestion;
use App\Repository\SuggestionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuggestionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SuggestionRepository $suggestions,
    ) {
    }

    #[Route('/suggestion/new', name: 'app_suggestion_new')]
    public function new(Request $request): Response
    {
        $form = $this->createFormBuilder(new Suggestion())
            ->setAction($this->generateUrl('app_suggestion_new'))
            ->add('name')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Volunteer to present the following topic as a talk' => 'Volunteer',
                    'Interested in attending a talk about the following topic' => 'Suggestion',
                ],
                'data' => 'Volunteer',
                'expanded' => true,
            ])
            ->add('topic', TextareaType::class, [
                'attr' => [
                    'rows' => 7,
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();
            $question->setDate(new DateTime());

            if (null === $question->getName()) {
                $question->setName('Anonymous');
            }

            $this->entityManager->persist($question);
            $this->entityManager->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        $pollSuggestions = $this->suggestions->findBy(['isPoll' => true]);

        return $this->render('suggestion/new-2023.html.twig', [
            'suggestion_form' => $form->createView(),
            'poll_suggestions' => $pollSuggestions,
        ]);
    }
}
