<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/admin/voiture")
 */
class VoitureController extends AbstractController
{
    /**
     * @Route("/", name="voiture_index", methods={"GET"})
     */
    public function index(VoitureRepository $voitureRepository): Response
    {
        return $this->render('voiture/index.html.twig', [
            'voitures' => $voitureRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="voiture_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo')->getData();
            if($photoFile){
                $originalName = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newName = $slugger->slug($originalName);
                $fileName = $newName . uniqid() . '.' . $photoFile->guessExtension();
                
                try {
                    $photoFile->move(
                        $this->getParameter('photo_directory'),
                        $fileName
                    );
                } catch (FileException $e){
                    // ...exception
                }

                $voiture->setPhoto($fileName);

            }

            $entityManager->persist($voiture);
            $entityManager->flush();

            return $this->redirectToRoute('voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voiture/new.html.twig', [
            'voiture' => $voiture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="voiture_show", methods={"GET"})
     */
    public function show(Voiture $voiture): Response
    {
        return $this->render('voiture/show.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="voiture_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voiture/edit.html.twig', [
            'voiture' => $voiture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="voiture_delete", methods={"POST"})
     */
    public function delete(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voiture->getId(), $request->request->get('_token'))) {
            $entityManager->remove($voiture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('voiture_index', [], Response::HTTP_SEE_OTHER);
    }
}
