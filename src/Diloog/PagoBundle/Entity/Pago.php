<?php

namespace Diloog\PagoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pago
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Pago
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
     * @ORM\Column(name="fecha_pago", type="date")
     */
    private $fechaPago;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantidad_cuotas", type="integer")
     */
    private $cantidadCuotas;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Diloog\PagoBundle\Entity\EstadoDeDeuda")
     * @ORM\JoinColumn(name="estado_deuda_id", referencedColumnName="id")
     */
    private $estadoDeuda;


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
     * Set fechaPago
     *
     * @param \DateTime $fechaPago
     * @return Pago
     */
    public function setFechaPago($fechaPago)
    {
        $this->fechaPago = $fechaPago;

        return $this;
    }

    /**
     * Get fechaPago
     *
     * @return \DateTime 
     */
    public function getFechaPago()
    {
        return $this->fechaPago;
    }

    /**
     * Set cantidadCuotas
     *
     * @param integer $cantidadCuotas
     * @return Pago
     */
    public function setCantidadCuotas($cantidadCuotas)
    {
        $this->cantidadCuotas = $cantidadCuotas;

        return $this;
    }

    /**
     * Get cantidadCuotas
     *
     * @return integer 
     */
    public function getCantidadCuotas()
    {
        return $this->cantidadCuotas;
    }

    /**
     * Set estadoDeuda
     *
     * @param string $estadoDeuda
     * @return Pago
     */
    public function setEstadoDeuda($estadoDeuda)
    {
        $this->estadoDeuda = $estadoDeuda;

        return $this;
    }

    /**
     * Get estadoDeuda
     *
     * @return string 
     */
    public function getEstadoDeuda()
    {
        return $this->estadoDeuda;
    }
}
