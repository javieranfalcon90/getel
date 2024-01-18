<?php

namespace App\Entity;

use App\Repository\DependenciaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DependenciaRepository::class)
 */
class Dependencia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity=Centro::class, inversedBy="dependencias")
     */
    private $centro;

    /**
     * @ORM\OneToMany(targetEntity=Departamento::class, mappedBy="dependencia")
     */
    private $departamentos;


    public function __construct()
    {
        $this->departamentos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nombre .' - '. $this->getCentro()->getNombre();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getCentro(): ?Centro
    {
        return $this->centro;
    }

    public function setCentro(?Centro $centro): self
    {
        $this->centro = $centro;

        return $this;
    }

    /**
     * @return Collection|Departamento[]
     */
    public function getDepartamentos(): Collection
    {
        return $this->departamentos;
    }

    public function addDepartamento(Departamento $departamento): self
    {
        if (!$this->departamentos->contains($departamento)) {
            $this->departamentos[] = $departamento;
            $departamento->setDependencia($this);
        }

        return $this;
    }

    public function removeDepartamento(Departamento $departamento): self
    {
        if ($this->departamentos->removeElement($departamento)) {
            // set the owning side to null (unless already changed)
            if ($departamento->getDependencia() === $this) {
                $departamento->setDependencia(null);
            }
        }

        return $this;
    }
}
