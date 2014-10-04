<?php

namespace Diloog\AfiliadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarjeta
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Tarjeta
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
     * @var integer
     *
     * @ORM\Column(name="numero_tarjeta", type="integer")
     */
    private $numeroTarjeta;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion_tarjeta", type="string", length=50)
     */
    private $descripcionTarjeta;

    /**
     * @var string
     *
     * @ORM\Column(name="vencimiento", type="string", length=6)
     */
    private $vencimiento;

    /**
     * @ORM\ManyToOne(targetEntity="Diloog\AfiliadoBundle\Entity\Afiliado")
     * @ORM\JoinColumn(name="afiliado_id", referencedColumnName="id")
     */
    private $afiliado;


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
     * Set numeroTarjeta
     *
     * @param integer $numeroTarjeta
     * @return Tarjeta
     */
    public function setNumeroTarjeta($numeroTarjeta)
    {
        $this->numeroTarjeta = $numeroTarjeta;

        return $this;
    }

    /**
     * Get numeroTarjeta
     *
     * @return integer 
     */
    public function getNumeroTarjeta()
    {
        return $this->numeroTarjeta;
    }

    /**
     * Set descripcionTarjeta
     *
     * @param string $descripcionTarjeta
     * @return Tarjeta
     */
    public function setDescripcionTarjeta($descripcionTarjeta)
    {
        $this->descripcionTarjeta = $descripcionTarjeta;

        return $this;
    }

    /**
     * Get descripcionTarjeta
     *
     * @return string 
     */
    public function getDescripcionTarjeta()
    {
        return $this->descripcionTarjeta;
    }

    /**
     * Set vencimiento
     *
     * @param string $vencimiento
     * @return Tarjeta
     */
    public function setVencimiento($vencimiento)
    {
        $this->vencimiento = $vencimiento;

        return $this;
    }

    /**
     * Get vencimiento
     *
     * @return string 
     */
    public function getVencimiento()
    {
        return $this->vencimiento;
    }

    /**
     * Set afiliado
     *
     * @return Tarjeta
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
}
