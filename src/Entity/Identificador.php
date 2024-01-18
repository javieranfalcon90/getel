<?php

namespace App\Entity;

use App\Repository\IdentificadorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IdentificadorRepository::class)
 */
class Identificador
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
    private $numero;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity=Departamento::class, inversedBy="identificadors")
     */
    private $departamento;

    /**
     * @ORM\OneToMany(targetEntity=Llamada::class, mappedBy="identificador")
     */
    private $llamadas;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $responsable;

    public function __construct()
    {
        $this->llamadas = new ArrayCollection();
    }

    public function __toString(){
        return $this->tipo.' '.$this->numero;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getDepartamento(): ?Departamento
    {
        return $this->departamento;
    }

    public function setDepartamento(?Departamento $departamento): self
    {
        $this->departamento = $departamento;

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
            $llamada->setIdentificador($this);
        }

        return $this;
    }

    public function removeLlamada(Llamada $llamada): self
    {
        if ($this->llamadas->removeElement($llamada)) {
            // set the owning side to null (unless already changed)
            if ($llamada->getIdentificador() === $this) {
                $llamada->setIdentificador(null);
            }
        }

        return $this;
    }

    public function getResponsable(): ?string
    {
        return $this->responsable;
    }

    public function setResponsable(string $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }
}
