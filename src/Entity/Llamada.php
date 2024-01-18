<?php

namespace App\Entity;

use App\Repository\LlamadaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LlamadaRepository::class)
 */
class Llamada
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $anno;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tronco;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telefono;

    /**
     * @ORM\Column(type="time")
     */
    private $duracion;

    /**
     * @ORM\ManyToOne(targetEntity=Localidad::class, inversedBy="llamadas")
     */
    private $localidad;

    /**
     * @ORM\Column(type="float")
     */
    private $costo;

    /**
     * @ORM\ManyToOne(targetEntity=Identificador::class, inversedBy="llamadas")
     */
    private $identificador;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnno(): ?string
    {
        return $this->anno;
    }

    public function setAnno(string $anno): self
    {
        $this->anno = $anno;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getTronco(): ?string
    {
        return $this->tronco;
    }

    public function setTronco(string $tronco): self
    {
        $this->tronco = $tronco;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getDuracion(): ?\DateTimeInterface
    {
        return $this->duracion;
    }

    public function setDuracion(\DateTimeInterface $duracion): self
    {
        $this->duracion = $duracion;

        return $this;
    }

    public function getLocalidad(): ?Localidad
    {
        return $this->localidad;
    }

    public function setLocalidad(?Localidad $localidad): self
    {
        $this->localidad = $localidad;

        return $this;
    }

    public function getCosto(): ?float
    {
        return $this->costo;
    }

    public function setCosto(float $costo): self
    {
        $this->costo = $costo;

        return $this;
    }

    public function getIdentificador(): ?Identificador
    {
        return $this->identificador;
    }

    public function setIdentificador(?Identificador $identificador): self
    {
        $this->identificador = $identificador;

        return $this;
    }
}
