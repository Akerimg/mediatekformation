<?php
namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminFormationsController extends AbstractController {

    private const ADMIN_FORMATIONS_TEMPLATE = "pages/admin/formations.html.twig";
    private const ADMIN_FORMATION_TEMPLATE = "pages/admin/formation.html.twig";

    private $formationRepository;
    private $categorieRepository;

    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/admin/formations', name: 'admin.formations')]
    public function index(): Response {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMIN_FORMATIONS_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/formations/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sort($champ, $ordre, $table=""): Response {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMIN_FORMATIONS_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/formations/recherche/{champ}/{table}', name: 'admin.formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::ADMIN_FORMATIONS_TEMPLATE, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    #[Route('/admin/formations/supprimer/{id}', name: 'admin.formations.supprimer', methods: ['POST'])]
    public function supprimer($id, Request $request): Response {
        $formation = $this->formationRepository->find($id);
        if (!$formation) {
            throw $this->createNotFoundException("Formation introuvable.");
        }
        if ($this->isCsrfTokenValid('supprimer' . $id, $request->request->get('_token'))) {
            $this->formationRepository->remove($formation);
        }
        return $this->redirectToRoute('admin.formations');
    }

    #[Route('/admin/formations/nouveau', name: 'admin.formations.nouveau')]
    public function nouveau(Request $request): Response {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formationRepository->add($formation);
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render(self::ADMIN_FORMATION_TEMPLATE, [
            'formation' => $formation,
            'formformation' => $form->createView()
        ]);
    }

    #[Route('/admin/formations/modifier/{id}', name: 'admin.formations.modifier')]
    public function modifier($id, Request $request): Response {
        $formation = $this->formationRepository->find($id);
        if (!$formation) {
            throw $this->createNotFoundException("Formation introuvable.");
        }
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->formationRepository->add($formation);
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render(self::ADMIN_FORMATION_TEMPLATE, [
            'formation' => $formation,
            'formformation' => $form->createView()
        ]);
    }
}
