<?php

namespace App\Entity;

use App\Repository\ZonaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ZonaRepository::class)
 */

/**
 * @ORM\Entity
 * @UniqueEntity("nombre", message= "No se pudo relizar la operación correctamente porque el valor {{ value }} del campo NOMBRE ya está registrado.")
 */

class Zona
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity=Tarifa::class, inversedBy="zonas")
     */
    private $tarifa;

    /**
     * @ORM\OneToMany(targetEntity=Localidad::class, mappedBy="zona")
     */
    private $localidads;

    public function __construct()
    {
        $this->localidads = new ArrayCollection();
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

    public function getTarifa(): ?Tarifa
    {
        return $this->tarifa;
    }

    public function setTarifa(?Tarifa $tarifa): self
    {
        $this->tarifa = $tarifa;

        return $this;
    }

    /**
     * @return Collection|Localidad[]
     */
    public function getLocalidads(): Collection
    {
        return $this->localidads;
    }

    public function addLocalidad(Localidad $localidad): self
    {
        if (!$this->localidads->contains($localidad)) {
            $this->localidads[] = $localidad;
            $localidad->setZona($this);
        }

        return $this;
    }

    public function removeLocalidad(Localidad $localidad): self
    {
        if ($this->localidads->removeElement($localidad)) {
            // set the owning side to null (unless already changed)
            if ($localidad->getZona() === $this) {
                $localidad->setZona(null);
            }
        }

        return $this;
    }
}
