<?php
/**
 * Created by PhpStorm.
 * User: Cristhian
 * Date: 29/10/14
 * Time: 23:04
 */

namespace Diloog\PagoBundle\Services;

use Diloog\AfiliadoBundle\Entity\Tarjeta;

class CheckoutManager {
    const GEN = 'cgcet2245ydhuz';

    private $tarjeta;

    private  $numerodeuda;

    private $importe;

    private $token;

    private $cuotas;

    private $codigoseguridad;

    /**
     * @param mixed $codigoseguridad
     */
    public function setCodigoseguridad($codigoseguridad)
    {
        $this->codigoseguridad = $codigoseguridad;
    }

    /**
     * @return mixed
     */
    public function getCodigoseguridad()
    {
        return $this->codigoseguridad;
    }

    /**
     * @param mixed $cuotas
     */
    public function setCuotas($cuotas)
    {
        $this->cuotas = $cuotas;
    }

    /**
     * @return mixed
     */
    public function getCuotas()
    {
        return $this->cuotas;
    }

    /**
     * @param mixed $importe
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;
    }

    /**
     * @return mixed
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * @param mixed $numerodeuda
     */
    public function setNumerodeuda($numerodeuda)
    {
        $this->numerodeuda = $numerodeuda;
    }

    /**
     * @return mixed
     */
    public function getNumerodeuda()
    {
        return $this->numerodeuda;
    }

    /**
     * @param Tarjeta $tarjeta
     */
    public function setTarjeta(Tarjeta $tarjeta)
    {
        $this->tarjeta = $tarjeta;
    }

    /**
     * @return Tarjeta
     */
    public function getTarjeta()
    {
        return $this->tarjeta;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    public function generateToken(){
        md5($this->getTarjeta()->getNumeroTarjeta().$this->getNumerodeuda().self::GEN.$this->getImporte());
        sha1('');

    }
} 