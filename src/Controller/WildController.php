<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild", name="wild_")
 */
class WildController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) :Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if(!$programs) {
            throw $this->createNotFoundException(
              'No program found in program\'s table.'
            );
        }

        $program = new Program();
        $form = $this->createForm(ProgramSearchType::class, $program);
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            $data = $form->getData();
        }

        return $this->render('wild/index.html.twig', [
            'programs' => $programs,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{slug}", name="show_program")
     * @param Program $program
     * @return Response
     */
    public function showByProgram(Program $program) : Response
    {
        $seasons = $program->getSeasons();

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    /**
     * @Route("/category/{categoryName}", name="show_category")
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName) :Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($categoryName)]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['Category' => $category], ['id' => 'DESC'], 3);

        return $this->render('wild/category.html.twig', [
            'category' => $category,
            'programs' => $programs,
        ]);
    }

    /**
     * @Route("/season/{id}", name="show_season")
     * @param Season $season
     * @return Response
     */
    public function showBySeason(Season $season) :Response
    {
        $episodes = $season->getEpisodes();
        $program = $season->getProgram();

        return $this->render('wild/season.html.twig', [
            'season' => $season,
            'episodes' => $episodes,
            'program' => $program,
        ]);
    }

    /**
     * @Route("/episode/{slug}", name="show_episode")
     * @param Episode $episode
     * @return Response
     */
    public function showEpisode(Episode $episode) :Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'season' => $season,
            'program' => $program,
        ]);
    }

    /**
     * @Route("/actor/{slug}", name="show_actor")
     * @param Actor $actor
     * @return Response
     */
    public function showActor(Actor $actor) :Response
    {
        $programs = $actor->getPrograms();

        return $this->render('wild/actor.html.twig', [
            'actor' => $actor,
            'programs' => $programs
        ]);
    }
}
