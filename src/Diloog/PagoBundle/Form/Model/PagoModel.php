<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 19/10/14
 * Time: 12:45
 */

namespace Diloog\PagoBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;




class PagoModel
{

    /**
     * @Assert\NotBlank()
     */
    protected $tipoTarjeta;

    /**
     * @Assert\NotBlank()
     */
    protected $numeroTarjeta;

    /**
     * @Assert\NotBlank()
     */
    protected $vencimiento;

    /**
     * @Assert\NotBlank()
     */
    protected $codigoSeguridad;

    /**
     * @Assert\NotBlank()
     */
    protected $titularNombre;

    /**
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    protected $dni;

    /**
     * @param mixed $codigoSeguridad
     */
    public function setCodigoSeguridad($codigoSeguridad)
    {
        $this->codigoSeguridad = $codigoSeguridad;
    }

    /**
     * @return mixed
     */
    public function getCodigoSeguridad()
    {
        return $this->codigoSeguridad;
    }

    /**
     * @param mixed $dni
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    /**
     * @return mixed
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * @param mixed $numeroTarjeta
     */
    public function setNumeroTarjeta($numeroTarjeta)
    {
        $this->numeroTarjeta = $numeroTarjeta;
    }

    /**
     * @return mixed
     */
    public function getNumeroTarjeta()
    {
        return $this->numeroTarjeta;
    }

    /**
     * @param mixed $tipoTarjeta
     */
    public function setTipoTarjeta($tipoTarjeta)
    {
        $this->tipoTarjeta = $tipoTarjeta;
    }

    /**
     * @return mixed
     */
    public function getTipoTarjeta()
    {
        return $this->tipoTarjeta;
    }

    /**
     * @param mixed $titularNombre
     */
    public function setTitularNombre($titularNombre)
    {
        $this->titularNombre = $titularNombre;
    }

    /**
     * @return mixed
     */
    public function getTitularNombre()
    {
        return $this->titularNombre;
    }

    /**
     * @param mixed $vencimiento
     */
    public function setVencimiento($vencimiento)
    {
        $this->vencimiento = $vencimiento;
    }

    /**
     * @return mixed
     */
    public function getVencimiento()
    {
        return $this->vencimiento;
    }



}