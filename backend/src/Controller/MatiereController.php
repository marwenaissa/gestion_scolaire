<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\MatiereRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Matiere;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\Query\ResultSetMapping;

class MatiereController extends AbstractController
{
    
    #[Route('/matierestest', name: 'app_matieres' ,methods:'GET')]
    public function indextest(SerializerInterface $serializer ,matiereRepository $matiererepository): JsonResponse
    {
        $matieres = $matiererepository->findAll();
        return $this->json($matieres);

        /* $professeurs = $professeurrepository->findAll();
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, [
            AbstractNormalizer::GROUPS => ['produit'],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT => 1,
        ]);
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $json = $serializer->serialize($professeurs, 'json');
        return new JsonResponse($json, 200, [], true); */

         /*  $categories = $produitrepo->findAll();
        $produits = $categorierepo->findAll();
        $data = [
            'categories' => $categories,
            'produits' => $produits,
        ];
        return $this->json($data, 200, [], ['groups' => ['categorie', 'produit']]); */
    }


    #[Route('/matieres', name: 'app_matiere' ,methods:'GET')]
    public function index(SerializerInterface $serializer ,matiereRepository $matiererepository): Response
    {
        $professeurs = $professeurrepository->findAll();
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


    #[Route('/matiere/{id}', name: 'app_matiere_show', methods: ['GET'])]
    public function show($id, SerializerInterface $serializer, matiereRepository $matiererepository): Response
    {
        $matiere = $matiererepository->find($id);
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
        $json = $serializer->serialize($matiere, 'json');
        return new JsonResponse($json, 200, [], true);
    }

    
    #[Route('/matierecreate', name: 'app_matiere_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $newmatiere = new matiere();
        $newmatiere->setSpecialite($data['specialite']);
        $newmatiere->setDescriptionProduit($data['descriptionproduit']);
        $professeurName = $data['professeur']['nom'];
        $professeurRepository = $entityManager->getRepository(Categorie::class);
        $professeur = $professeurRepository->findOneBy(['nom' => $professeurName]);
        $newmatiere->setprofesseur($professeur);
        $entityManager->persist($newmatiere);
        $entityManager->flush();
        $jsonContent = $serializer->serialize($newmatiere, 'json', []);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/produit/{id}', name: 'app_produit_update', methods: ['PUT'])]
    public function createput($id , Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $matiereRepository = $entityManager->getRepository(Produit::class);
        $existingmatiere = $matiereRepository->find($id);
        if (!$existingmatiere) {
            return new JsonResponse(['message' => 'matiere not found'], 404);
        }
        $existingmatiere->setNom($data['nom']);
        $existingmatiere->setSpecialite($data['specialite']);
        $professeurName = $data['professeur']['nom'];
        $professeurRepository = $entityManager->getRepository(professeur::class);
        $professeur = $professeurRepository->findOneBy(['nom' => $professeurName]);
        $newprofesseur->setprofesseur($professeur);
        $entityManager->persist($existingmatiere);
        $entityManager->flush();
        $jsonContent = $serializer->serialize($existingmatiere, 'json', ['groups' => 'produit:read']);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/matiere/{id}', name: 'deletematiere', methods: ['DELETE'])]
    public function delete( $id, EntityManagerInterface $em, ProfesseurRepository $professeurprofesseur): JsonResponse
    {
        $professeur = $professeurprofesseur->find($id);
        $em->remove($professeur);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


}
