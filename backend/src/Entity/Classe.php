<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ClasseRepository::class)]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbretudiants = null;

    /**
     * @var Collection<int, Etudiant>
     */
    #[ORM\OneToMany(targetEntity: Etudiant::class, mappedBy: 'classe')]
    private Collection $etudiants;

    /**
     * @var Collection<int, Matiere>
     */
    #[ORM\ManyToMany(targetEntity: Matiere::class, inversedBy: 'classes')]
    private Collection $matiere;

    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
        $this->matiere = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNbretudiants(): ?int
    {
        return $this->nbretudiants;
    }

    public function setNbretudiants(?int $nbretudiants): static
    {
        $this->nbretudiants = $nbretudiants;

        return $this;
    }

    /**
     * @return Collection<int, Etudiant>
     */
    /* public function getEtudiants(): Collection
    {
        return $this->etudiants;
    } */


  

    public function addEtudiant(Etudiant $etudiant): static
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants->add($etudiant);
            $etudiant->setClasse($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): static
    {
        if ($this->etudiants->removeElement($etudiant)) {
            // set the owning side to null (unless already changed)
            if ($etudiant->getClasse() === $this) {
                $etudiant->setClasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Matiere>
     */
    /* public function getMatiere(): Collection
    {
        return $this->matiere;
    } */

    public function addMatiere(Matiere $matiere): static
    {
        if (!$this->matiere->contains($matiere)) {
            $this->matiere->add($matiere);
        }

        return $this;
    }

    public function removeMatiere(Matiere $matiere): static
    {
        $this->matiere->removeElement($matiere);

        return $this;
    }

    

    

   
   

    

    
  

    

    

   
}
