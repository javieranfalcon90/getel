<?php

namespace App\Entity;

use App\Repository\CentroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CentroRepository::class)
 */
class Centro
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
     * @ORM\OneToMany(targetEntity=Dependencia::class, mappedBy="centro")
     */
    private $dependencias;

    public function __construct()
    {
        $this->dependencias = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nombre;
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

    /**
     * @return Collection|Dependencia[]
     */
    public function getDependencias(): Collection
    {
        return $this->dependencias;
    }

    public function addDependencia(Dependencia $dependencia): self
    {
        if (!$this->dependencias->contains($dependencia)) {
            $this->dependencias[] = $dependencia;
            $dependencia->setCentro($this);
        }

        return $this;
    }

    public function removeDependencia(Dependencia $dependencia): self
    {
        if ($this->dependencias->removeElement($dependencia)) {
            // set the owning side to null (unless already changed)
            if ($dependencia->getCentro() === $this) {
                $dependencia->setCentro(null);
            }
        }

        return $this;
    }

}
