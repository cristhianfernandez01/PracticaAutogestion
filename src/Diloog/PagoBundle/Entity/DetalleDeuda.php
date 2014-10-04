<?php

namespace Diloog\PagoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleDeuda
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class DetalleDeuda
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
     * @var string
     *
     * @ORM\Column(name="concepto", type="string", length=255)
     */
    private $concepto;

    /**
     * @var integer
     *
     * @ORM\Column(name="codigo", type="integer")
     */
    private $codigo;

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal", type="float")
     */
    private $subtotal;

    /**
     *
     * @ORM\ManyToOne(targetEntity="\Diloog\PagoBundle\Entity\EstadoDeDeuda")
     * @ORM\JoinColumn(name="estado_deuda_id", referencedColumnName="id")
     */
    private $estadoDeuda;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="\Diloog\PagoBundle\Entity\SubdetalleDeuda", mappedBy="detalledeuda")
     */

    private $subdetallesdeuda;



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
     * Set concepto
     *
     * @param string $concepto
     * @return DetalleDeuda
     */
    public function setConcepto($concepto)
    {
        $this->concepto = $concepto;

        return $this;
    }

    /**
     * Get concepto
     *
     * @return string 
     */
    public function getConcepto()
    {
        return $this->concepto;
    }

    /**
     * Set codigo
     *
     * @param integer $codigo
     * @return DetalleDeuda
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set subtotal
     *
     * @param float $subtotal
     * @return DetalleDeuda
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return float 
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set estadoDeuda
     *
     * @param string $estadoDeuda
     * @return DetalleDeuda
     */
    public function setEstadoDeuda(\Diloog\PagoBundle\Entity\EstadoDeDeuda $estadoDeuda)
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

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $subdetallesdeuda
     */
    public function setSubdetallesdeuda($subdetallesdeuda)
    {
        $this->subdetallesdeuda = $subdetallesdeuda;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSubdetallesdeuda()
    {
        return $this->subdetallesdeuda;
    }

    public function addSubdetalledeuda(\Diloog\PagoBundle\Entity\SubdetalleDeuda $subdetalle)
    {
        $this->subdetallesdeuda[] = $subdetalle;
    }


}
