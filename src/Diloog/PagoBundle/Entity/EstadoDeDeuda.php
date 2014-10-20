<?php

namespace Diloog\PagoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoDeDeuda
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Diloog\PagoBundle\Entity\EstadoDeDeudaRepository")
 */
class EstadoDeDeuda
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_emision", type="date")
     */
    private $fechaEmision;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_total", type="float")
     */
    private $importeTotal;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pagada", type="boolean")
     */
    private $pagada;


    /**
     * @var boolean
     *
     * @ORM\Column(name="activa", type="boolean")
     */
    private $activa;



    /**
     *
     * @ORM\ManyToOne(targetEntity="\Diloog\AfiliadoBundle\Entity\Afiliado", inversedBy="estadosdedeuda")
     * @ORM\JoinColumn(name="afiliado_id", referencedColumnName="id")
     */
    private $afiliado;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="\Diloog\PagoBundle\Entity\DetalleDeuda", mappedBy="estadoDeuda")
     */
    private $detallesdeuda;

    function __construct()
    {
        $this->detallesdeuda = new ArrayCollection();

    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaEmision
     *
     * @param \DateTime $fechaEmision
     * @return EstadoDeDeuda
     */
    public function setFechaEmision($fechaEmision)
    {
        $this->fechaEmision = $fechaEmision;

        return $this;
    }

    /**
     * Get fechaEmision
     *
     * @return \DateTime 
     */
    public function getFechaEmision()
    {
        return $this->fechaEmision;
    }

    /**
     * Set importeTotal
     *
     * @param float $importeTotal
     * @return EstadoDeDeuda
     */
    public function setImporteTotal($importeTotal)
    {
        $this->importeTotal = $importeTotal;

        return $this;
    }

    /**
     * Get importeTotal
     *
     * @return float 
     */
    public function getImporteTotal()
    {
        return $this->importeTotal;
    }

    /**
     * Set pagada
     *
     * @param boolean $pagada
     * @return EstadoDeDeuda
     */
    public function setPagada($pagada)
    {
        $this->pagada = $pagada;

        return $this;
    }

    /**
     * Get pagada
     *
     * @return boolean 
     */
    public function isPagada()
    {
        return $this->pagada;
    }


    /**
     * @param boolean $activa
     */
    public function setActiva($activa)
    {
        $this->activa = $activa;
    }

    /**
     * @return boolean
     */
    public function isActiva()
    {
        return $this->activa;
    }

    /**
     * Set afiliado
     * @return EstadoDeDeuda
     */
    public function setAfiliado(\Diloog\AfiliadoBundle\Entity\Afiliado $afiliado)
    {
        $this->afiliado = $afiliado;

        return $this;
    }

    /**
     * Get afiliado
     *
     * @return string 
     */
    public function getAfiliado()
    {
        return $this->afiliado;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $detallesdeuda
     */
    public function setDetallesDeuda($detallesdeuda)
    {
        $this->detallesdeuda = $detallesdeuda;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDetallesDeuda()
    {
        return $this->detallesdeuda;
    }

    public function addDetalleDeuda(\Diloog\PagoBundle\Entity\DetalleDeuda $detalledeuda){
        $this->detallesdeuda[]= $detalledeuda;

    }


}
