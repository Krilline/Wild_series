<?php

namespace App\Controller;

use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/program")
 */
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="program_index", methods={"GET"})
     */
    public function index(ProgramRepository $programRepository): Response
    {
        return $this->render('program/index.html.twig', [
            'programs' => $programRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="program_new", methods={"GET","POST"})
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function new(Request $request, Slugify $slugify, MailerInterface $mailer): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $entityManager->persist($program);
            $entityManager->flush();

            $this->addFlash('success', "Une nouvelle série a été ajoutée avec succés !");

            $email = (new TemplatedEmail())
                ->from($this->getParameter('mailer_from'))
                ->to('andraurelien@yahoo.fr')
                ->subject("Une nouvelle série vient d'etre publiée !")
                ->htmlTemplate('emails/notification.html.twig')
                ->context([
                    'program' => $program,
                ]);

            $mailer->send($email);

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/new.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="program_show", methods={"GET"})
     */
    public function show(Program $program): Response
    {
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="program_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Program $program, Slugify $slugify): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $entityManager->persist($program);
            $entityManager->flush();

            $this->addFlash('success', "La série a bien été modifiée avec succés !");

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="program_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Program $program): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($program);
            $entityManager->flush();

            $this->addFlash('danger', "La série a été supprimée avec succés !");
        }

        return $this->redirectToRoute('program_index');
    }
}
