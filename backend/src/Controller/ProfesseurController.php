<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfesseurController extends AbstractController
{
    #[Route('/etudiantstest', name: 'app_etudiants' ,methods:'GET')]
    public function indextest(SerializerInterface $serializer ,EtudiantRepository $etudiantrepository): JsonResponse
    {
        $etudiants = $etudiantrepository->findAll();
        return $this->json($etudiants);

        /* $classes = $classerepository->findAll();
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, [
            AbstractNormalizer::GROUPS => ['produit'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
        ]);
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $json = $serializer->serialize($classes, 'json');
        return new JsonResponse($json, 200, [], true); */

         /*  $categories = $produitrepo->findAll();
        $produits = $categorierepo->findAll();
        $data = [
            'categories' => $categories,
            'produits' => $produits,
        ];
        return $this->json($data, 200, [], ['groups' => ['categorie', 'produit']]); */
    }


    #[Route('/professeurs', name: 'app_professeur' ,methods:'GET')]
    public function index(SerializerInterface $serializer ,EtudiantRepository $etudiantrepository): Response
    {
        $professeurs = $classerepository->findAll();
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $circularReferenceHandler = function ($object) {
            return $object->getId();
        };
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => $circularReferenceHandler,
            AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
        ]);
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $json = $serializer->serialize($professeurs, 'json');
        return new JsonResponse($json, 200, [], true);

    }


    #[Route('/etudiant/{id}', name: 'app_etudiant_show', methods: ['GET'])]
    public function show($id, SerializerInterface $serializer, EtudiantRepository $etudiantrepository): Response
    {
        $professeur = $etudiantrepository->find($id);
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $circularReferenceHandler = function ($object) {
            return $object->getId();
        };
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => $circularReferenceHandler,
            AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
        ]);
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $json = $serializer->serialize($professeur, 'json');
        return new JsonResponse($json, 200, [], true);
    }

    
    #[Route('/professeurcreate', name: 'app_professeur_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $newProfesseur = new Professeur();
        $newProfesseur->setSpecialite($data['specialite']);
        $newProfesseur->setDescriptionProduit($data['descriptionproduit']);
        $matiereName = $data['matiere']['nom'];
        $matiereRepository = $entityManager->getRepository(Matiere::class);
        $matiere = $matiereRepository->findOneBy(['nom' => $matiereName]);
        $newMatiere->setClasse($matiere);
        $entityManager->persist($newMatiere);
        $entityManager->flush();
        $jsonContent = $serializer->serialize($newEtudiant, 'json', []);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/professeur/{id}', name: 'app_professeur_update', methods: ['PUT'])]
    public function createput($id , Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $professeurRepository = $entityManager->getRepository(Produit::class);
        $existingProfesseur = $professeurRepository->find($id);
        if (!$existingProfesseur) {
            return new JsonResponse(['message' => 'Etudiant not found'], 404);
        }
        $existingProfesseur->setNom($data['nom']);
        $existingProfesseur->setSpecialite($data['specialite']);
        $matiereName = $data['matiere']['nom'];
        $matiereRepository = $entityManager->getRepository(Matiere::class);
        $matiere = $classeRepository->findOneBy(['nom' => $matiereName]);
        $existingProfesseur->setMatiere($matiere);
        $entityManager->persist($existingProfesseur);
        $entityManager->flush();
        $jsonContent = $serializer->serialize($existingProfesseur, 'json', []);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/professeur/{id}', name: 'deleteprofesseur', methods: ['DELETE'])]
    public function delete( $id, EntityManagerInterface $em, ProfesseurRepository $professeurprofesseur): JsonResponse
    {
        $professeur = $professeurprofesseur->find($id);
        $em->remove($professeur);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
