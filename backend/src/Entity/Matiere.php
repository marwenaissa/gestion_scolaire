<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatiereRepository::class)]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $programme = null;

    #[ORM\Column]
    private ?float $nbrheure = null;

    /**
     * @var Collection<int, Classe>
     */
    #[ORM\ManyToMany(targetEntity: Classe::class, mappedBy: 'matiere')]
    private Collection $classes;

    #[ORM\ManyToOne(inversedBy: 'matieres')]
    private ?Professeur $professeur = null;

    public function __construct()
    {
        $this->classes = new ArrayCollection();
    }


   
   
    


    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?float
    {
        return $this->nom;
    }

    public function setNom(float $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getProgramme(): ?string
    {
        return $this->programme;
    }

    public function setProgramme(?string $programme): static
    {
        $this->programme = $programme;

        return $this;
    }

    public function getNbrheure(): ?float
    {
        return $this->nbrheure;
    }

    public function setNbrheure(float $nbrheure): static
    {
        $this->nbrheure = $nbrheure;

        return $this;
    }

    /**
     * @return Collection<int, Classe>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classe $class): static
    {
        if (!$this->classes->contains($class)) {
            $this->classes->add($class);
            $class->addMatiere($this);
        }

        return $this;
    }

    public function removeClass(Classe $class): static
    {
        if ($this->classes->removeElement($class)) {
            $class->removeMatiere($this);
        }

        return $this;
    }

    public function getProfesseur(): ?Professeur
    {
        return $this->professeur;
    }

    public function setProfesseur(?Professeur $professeur): static
    {
        $this->professeur = $professeur;

        return $this;
    }

    

   

   


    

    


    
}
