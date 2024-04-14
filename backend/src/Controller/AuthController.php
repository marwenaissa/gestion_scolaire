<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Entity\Etudiant;
use App\Entity\Professeur;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AuthController extends AbstractController
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(
        JWTTokenManagerInterface $JWTManager,
        Request $request,
        // RefreshTokenManagerInterface $refreshTokenManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        // Obtient les identifiants de l'utilisateur depuis la requête (par exemple, email et mot de passe)
        $credentials = json_decode($request->getContent(), true);
       
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        // Vérifie si l'email ou le mot de passe est manquant
        if (empty($email) || empty($password)) {
            return new JsonResponse(['message' => 'Email et mot de passe sont nécessaires'], Response::HTTP_BAD_REQUEST);
        }

        // Trouve l'utilisateur par email
        $user = $userRepository->findOneByEmail($email);

        // Vérifie si un utilisateur avec l'email fourni existe
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        // Vérifie si le mot de passe fourni est correct
        if (!$userPasswordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        // Génère un jeton JWT pour l'utilisateur authentifié
        $token = $JWTManager->create($user);

        return new JsonResponse(['token' => $token], Response::HTTP_CREATED);
    }

    #[Route('/signup', name: 'signup', methods: ['POST'])]
    public function signup(
        UserRepository $userRepository,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        Request $request,
        JWTTokenManagerInterface $JWTManager,
        TagAwareCacheInterface $cache
    ): JsonResponse {
        // Désérialise la requête JSON en un tableau associatif
        $requestData = json_decode($request->getContent(), true);
    
        // Récupère l'email et le mot de passe de la requête
        $email = $requestData['email'] ?? null;
        $password = $requestData['password'] ?? null;
        $nomutilisateur = $requestData['nomutilisateur'] ?? null;
        $nom = $requestData['nom'] ?? null;
        $prenom = $requestData['prenom'] ?? null;
        
        // Vérifie si l'email ou le mot de passe est vide
        if (empty($email) || empty($password)) {
            return new JsonResponse(['message' => 'Email et mot de passe sont nécessaires'], Response::HTTP_BAD_REQUEST);
        }
    
        // Vérifie si l'utilisateur avec l'email fourni existe déjà
        $existingUser = $userRepository->findOneByEmail($email);
    
        if ($existingUser) {
            return new JsonResponse(['message' => 'Email existe déjà'], Response::HTTP_BAD_REQUEST);
        }

        // Crée une nouvelle entité User
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $user->setNomutilisateur($nomutilisateur);
        $user->setNom($nom);
        $user->setPrenom($prenom);

         // Persiste et enregistre l'entité User dans la base de données
         $em->persist($user);
         $em->flush();

         $dernierId = $user->getId();
         $lastuser =  $em->getRepository(User::class)->find($dernierId);


        $role = $requestData['role'] ?? null;
        if($role == "PROFESSEUR"){
            $professeur = new Professeur();
            $specialisation = $requestData['specialisation'] ?? null;
            $experience = $requestData['experience'] ?? null;
            $dateemploi = $requestData['dateemploi'] ?? null;
            $dateemploi = \DateTime::createFromFormat('Y-m-d', $dateemploi);
            if ($dateemploi instanceof \DateTimeInterface) {
                $professeur->setDateemploi($dateemploi);
            } 
            $user->setRoles(["ROLE_PROFESSEUR"]);
            $professeur->setUser($lastuser);
            $professeur->setSpecialisation($specialisation);
            $professeur->setExperience($experience);
            $professeur->setDateemploi($dateemploi);
            $em->persist($professeur);
        }else{
            $etudiant = new Etudiant();
            $etudiant->setUser($lastuser);
            $user->setRoles(["ROLE_ETUDIANT"]);
            $specialite = $requestData['specialite'] ?? null;
            $etudiant->setSpecialite("specialite");
            $em->persist($etudiant);
        }

        $em->flush();
    
        // Génère un jeton JWT pour le nouvel utilisateur
        $token = $JWTManager->create($user);
    
        return new JsonResponse(['token' => $token], Response::HTTP_CREATED);   
    }


}
