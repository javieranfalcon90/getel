<?php

namespace App\Entity;

use App\Repository\LocalidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocalidadRepository::class)
 */
class Localidad
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
     * @ORM\Column(type="string", length=255)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $inicial;

    /**
     * @ORM\ManyToOne(targetEntity=Zona::class, inversedBy="localidads")
     */
    private $zona;

    /**
     * @ORM\OneToMany(targetEntity=Llamada::class, mappedBy="localidad")
     */
    private $llamadas;

    public function __construct()
    {
        $this->llamadas = new ArrayCollection();
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

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getInicial(): ?string
    {
        return $this->inicial;
    }

    public function setInicial(string $inicial): self
    {
        $this->inicial = $inicial;

        return $this;
    }

    public function getZona(): ?Zona
    {
        return $this->zona;
    }

    public function setZona(?Zona $zona): self
    {
        $this->zona = $zona;

        return $this;
    }

    /**
     * @return Collection|Llamada[]
     */
    public function getLlamadas(): Collection
    {
        return $this->llamadas;
    }

    public function addLlamada(Llamada $llamada): self
    {
        if (!$this->llamadas->contains($llamada)) {
            $this->llamadas[] = $llamada;
            $llamada->setLocalidad($this);
        }

        return $this;
    }

    public function removeLlamada(Llamada $llamada): self
    {
        if ($this->llamadas->removeElement($llamada)) {
            // set the owning side to null (unless already changed)
            if ($llamada->getLocalidad() === $this) {
                $llamada->setLocalidad(null);
            }
        }

        return $this;
    }
}
