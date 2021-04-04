<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Talk;
use App\Repository\TalkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('/question/new', name: 'app_question_new')]
    public function new(Request $request): Response
    {
        $form = $this->createFormBuilder(new Question())
            ->setAction($this->generateUrl('app_question_new'))
            ->add('name')
            ->add('talk', EntityType::class, [
                'class' => Talk::class,
                'required' => false,
                'query_builder' => fn (TalkRepository $talks) => $talks->createQueryBuilder('q')
                    ->andWhere(sprintf('q.%s = false', 'isArchived'))
            ])
            ->add('question')
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();

            if (null === $question->getName()) {
                $question->setName('Anonymous');
            }

            $this->entityManager->persist($question);
            $this->entityManager->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('question/new.html.twig', ['question_form' => $form->createView()]);
    }
}
