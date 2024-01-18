<?php

namespace App\Entity;

use App\Repository\TarifaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=TarifaRepository::class)
 */


class Tarifa
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $nombre;

    /**
     * @ORM\Column(type="time")
     */
    private $desdediurno;

    /**
     * @ORM\Column(type="time")
     */
    private $hastadiurno;

    /**
     * @ORM\Column(type="float")
     */
    private $tarifadiurno;

    /**
     * @ORM\Column(type="time")
     */
    private $desdenocturno;

    /**
     * @ORM\Column(type="time")
     */
    private $hastanocturno;

    /**
     * @ORM\Column(type="float")
     */
    private $tarifanocturno;

    /**
     * @ORM\OneToMany(targetEntity=Zona::class, mappedBy="tarifa")
     */
    private $zonas;

    public function __construct()
    {
        $this->zonas = new ArrayCollection();
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

    public function getDesdediurno(): ?\DateTimeInterface
    {
        return $this->desdediurno;
    }

    public function setDesdediurno(\DateTimeInterface $desdediurno): self
    {
        $this->desdediurno = $desdediurno;

        return $this;
    }

    public function getHastadiurno(): ?\DateTimeInterface
    {
        return $this->hastadiurno;
    }

    public function setHastadiurno(\DateTimeInterface $hastadiurno): self
    {
        $this->hastadiurno = $hastadiurno;

        return $this;
    }

    public function getTarifadiurno(): ?float
    {
        return $this->tarifadiurno;
    }

    public function setTarifadiurno(float $tarifadiurno): self
    {
        $this->tarifadiurno = $tarifadiurno;

        return $this;
    }

    public function getDesdenocturno(): ?\DateTimeInterface
    {
        return $this->desdenocturno;
    }

    public function setDesdenocturno(\DateTimeInterface $desdenocturno): self
    {
        $this->desdenocturno = $desdenocturno;

        return $this;
    }

    public function getHastanocturno(): ?\DateTimeInterface
    {
        return $this->hastanocturno;
    }

    public function setHastanocturno(\DateTimeInterface $hastanocturno): self
    {
        $this->hastanocturno = $hastanocturno;

        return $this;
    }

    public function getTarifanocturno(): ?float
    {
        return $this->tarifanocturno;
    }

    public function setTarifanocturno(float $tarifanocturno): self
    {
        $this->tarifanocturno = $tarifanocturno;

        return $this;
    }

    /**
     * @return Collection|Zona[]
     */
    public function getZonas(): Collection
    {
        return $this->zonas;
    }

    public function addZona(Zona $zona): self
    {
        if (!$this->zonas->contains($zona)) {
            $this->zonas[] = $zona;
            $zona->setTarifa($this);
        }

        return $this;
    }

    public function removeZona(Zona $zona): self
    {
        if ($this->zonas->removeElement($zona)) {
            // set the owning side to null (unless already changed)
            if ($zona->getTarifa() === $this) {
                $zona->setTarifa(null);
            }
        }

        return $this;
    }
}
