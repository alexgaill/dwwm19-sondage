<?php

namespace App\Entity;

use App\Repository\SondageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SondageRepository::class)]
class Sondage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\Length(
        min:5,
        max:100,
        minMessage: "Le nom du sondage doit contenir au minimum {{ limit }} caractères",
        maxMessage: "Le nom du sondage doit contenir au maximum {{ limit }} caractères"
    )]
    #[Assert\NotNull(
        message:"Cette information est obligatoire"
    )]
    private $nom;

    #[ORM\OneToMany(mappedBy: 'sondage', targetEntity: Question::class, orphanRemoval: true)]
    private $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setSondage($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getSondage() === $this) {
                $question->setSondage(null);
            }
        }

        return $this;
    }
}
