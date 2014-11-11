<?php

namespace Diloog\PagoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubdetalleDeuda
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SubdetalleDeuda
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
     * @ORM\Column(name="fecha_vencimiento", type="date")
     */
    private $fechaVencimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="subconcepto", type="string", length=255)
     */
    private $subconcepto;

    /**
     * @var float
     *
     * @ORM\Column(name="importe", type="float")
     */
    private $importe;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="Diloog\PagoBundle\Entity\DetalleDeuda", inversedBy="subdetallesdeuda", cascade={"persist"})
     * @ORM\JoinColumn(name="detalle_deuda_id", referencedColumnName="id")
     */
    private $detalleDeuda;


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
     * Set fechaVencimiento
     *
     * @param \DateTime $fechaVencimiento
     * @return SubdetalleDeuda
     */
    public function setFechaVencimiento($fechaVencimiento)
    {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return \DateTime 
     */
    public function getFechaVencimiento()
    {
        return $this->fechaVencimiento;
    }

    /**
     * Set subconcepto
     *
     * @param string $subconcepto
     * @return SubdetalleDeuda
     */
    public function setSubconcepto($subconcepto)
    {
        $this->subconcepto = $subconcepto;

        return $this;
    }

    /**
     * Get subconcepto
     *
     * @return string 
     */
    public function getSubconcepto()
    {
        return $this->subconcepto;
    }

    /**
     * Set importe
     *
     * @param float $importe
     * @return SubdetalleDeuda
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return float 
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set detalleDeuda
     *
     * @param string $detalleDeuda
     * @return SubdetalleDeuda
     */
    public function setDetalleDeuda(\Diloog\PagoBundle\Entity\DetalleDeuda $detalleDeuda)
    {
        $this->detalleDeuda = $detalleDeuda;

        return $this;
    }

    /**
     * Get detalleDeuda
     *
     * @return string 
     */
    public function getDetalleDeuda()
    {
        return $this->detalleDeuda;
    }
}
