<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\EtudiantRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Etudiant;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\Query\ResultSetMapping;

class EtudiantController extends AbstractController
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


    #[Route('/etudiants', name: 'app_etudiant' ,methods:'GET')]
    public function index(SerializerInterface $serializer ,EtudiantRepository $etudiantrepository): Response
    {
        $classes = $classerepository->findAll();
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
        $json = $serializer->serialize($classes, 'json');
        return new JsonResponse($json, 200, [], true);

    }


    #[Route('/etudiant/{id}', name: 'app_etudiant_show', methods: ['GET'])]
    public function show($id, SerializerInterface $serializer, EtudiantRepository $etudiantrepository): Response
    {
        $etudiant = $etudiantrepository->find($id);
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
        $json = $serializer->serialize($etudiant, 'json');
        return new JsonResponse($json, 200, [], true);
    }

    
    #[Route('/etudiantcreate', name: 'app_etudiant_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $newEtudiant = new Etudiant();
        $newEtudiant->setSpecialite($data['specialite']);
        $newEtudiant->setDescriptionProduit($data['descriptionproduit']);
        $classeName = $data['classe']['nom'];
        $classeRepository = $entityManager->getRepository(Categorie::class);
        $classe = $classeRepository->findOneBy(['nom' => $classeName]);
        $newEtudiant->setClasse($classe);
        $entityManager->persist($newEtudiant);
        $entityManager->flush();
        $jsonContent = $serializer->serialize($newEtudiant, 'json', []);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/produit/{id}', name: 'app_produit_update', methods: ['PUT'])]
    public function createput($id , Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $etudiantRepository = $entityManager->getRepository(Produit::class);
        $existingEtudiant = $etudiantRepository->find($id);
        if (!$existingEtudiant) {
            return new JsonResponse(['message' => 'Etudiant not found'], 404);
        }
        $existingEtudiant->setNom($data['nom']);
        $existingEtudiant->setSpecialite($data['specialite']);
        $classeName = $data['classe']['nom'];
        $classeRepository = $entityManager->getRepository(Classe::class);
        $classe = $classeRepository->findOneBy(['nom' => $classeName]);
        $newClasse->setClasse($classe);
        $entityManager->persist($existingEtudiant);
        $entityManager->flush();
        $jsonContent = $serializer->serialize($existingEtudiant, 'json', ['groups' => 'produit:read']);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/etudiant/{id}', name: 'deleteetudiant', methods: ['DELETE'])]
    public function delete( $id, EntityManagerInterface $em,EtudiantRepository $etudiantRepository): JsonResponse
    {
        $etudiant = $etudiantRepository->find($id);
        $em->remove($etudiant);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}
