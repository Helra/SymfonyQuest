<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

Class WildController extends AbstractController
{
    /**
     * Show all rows from Program’s entity
     *
     * @Route("/index", name="wild_index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        foreach ( $programs as $program)
        {
            $programsUrl[] = str_replace(' ', '-', strtolower($program->getTitle()));
        }

        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs,
                'urls' => $programsUrl,
                ]
        );
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="wild_show")
     * @return Response
     */
    public function show(?string $slug):Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }

        $seasonsInPrograms = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program'=> $program]);

        if (!$seasonsInPrograms) {
            throw $this->createNotFoundException(
                'No season(s) found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
            'seasons' => $seasonsInPrograms,
        ]);
    }

    /**
     * @Route("/category/{categoryName}", name="show_category")
     * @param string $categoryName
     * @return Response
     */

    public function showByCategory(string $categoryName) : Response
    {

        if (!$categoryName) {
            throw $this
            ->createNotFoundException('No category has been sent to find a category in category\'s table.');
        }
        $category = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );

        $categoryId = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(['name' => mb_strtolower($category)]);

        $programsInCategory = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category'=> $categoryId]);

        if (!$programsInCategory) {
            throw $this->createNotFoundException(
                'No program with '.$category.' title, found in program\'s table.'
            );
        }

        $programesUrl = str_replace(' ', '-', strtolower($programsInCategory));

        return $this->render('wild/index_category.html.twig', [
            'programs' => $programsInCategory,
            'category'  => $category,
            'urls' => $programesUrl,
        ]);
    }

    /**
     * @Route("/{idProgram}/season", name="show_season")
     * @param int $idProgram
     * @return Response
     */
    public function showSeasons(int $idProgram) : Response
    {
        if (!$idProgram) {
            throw $this
                ->createNotFoundException('No season has been sent to find a season in program\'s table.');
        }

        $seasonInPrograms = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program'=> $idProgram]);

        return $this->render('wild/index_season.html.twig', ['seasons' => $seasonInPrograms]);
    }

    /**
     * @Route("/show/{idSeason}/episode", name="index_episode")
     * @param int $idSeason
     * @return Response
     */
    public function indexEpisode(int $idSeason) : Response
    {
        if (!$idSeason) {
            throw $this
                ->createNotFoundException('No episode has been sent to find a episode in season\'s table.');
        }

        $episodesInSeason = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findBy(['season'=> $idSeason]);

        return $this->render('wild/index_episode.html.twig', ['episodes' => $episodesInSeason]);
    }

    /**
     * @Route("/{id}", name="show_episode")
     * @param Episode $episode
     * @return Response
     */
    public function showEpisode(Episode $episode) : Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/show_episode.html.twig', ['episode' => $episode , 'season' => $season , 'program' => $program]);
    }

}
