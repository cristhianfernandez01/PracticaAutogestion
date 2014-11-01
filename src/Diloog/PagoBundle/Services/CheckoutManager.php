<?php
/**
 * Created by PhpStorm.
 * User: Cristhian
 * Date: 29/10/14
 * Time: 23:04
 */

namespace Diloog\PagoBundle\Services;

use Diloog\AfiliadoBundle\Entity\Tarjeta;
use Diloog\PagoBundle\Services\Model\CheckoutServer;

class CheckoutManager {
    const GEN = 'cgcet2245ydhuz';

    /**
     * @var @Diloog\AfiliadoBundle\Entity\Tarjeta;
     */
    private $tarjeta;

    /**
     * @var int
     */
    private  $numerodeuda;

    /**
     * @var float
     */
    private $importe;

    /**
     * @var string
     */
    private $token;

    /**
     * @var int
     */
    private $cuotas;

    /**
     * @var int
     */
    private $codigoseguridad;

    /**
     * @var @Diloog\PagoBundle\Services\Model\CheckoutServer
     */
    private $server;


    public function __construct(){
        $this->server = new CheckoutServer();
    }

    /**
     * @param CheckoutServer $server
     */

    public function setServer(CheckoutServer $server)
    {
        $this->server = $server;
    }

    /**
     * @return @Diloog\PagoBundle\Services\Model\CheckoutServer
     */
    public function getServer()
    {
        return $this->server;
    }


    public function definirDatos(Tarjeta $tarjeta, $numeroDeuda, $importe, $cuotas, $codigoseguridad){
        $this->setTarjeta($tarjeta);
        $this->setNumerodeuda($numeroDeuda);
        $this->setImporte($importe);
        $this->setCuotas($cuotas);
        $this->setCodigoseguridad($codigoseguridad);
    }

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
        $token = sha1(md5($this->getTarjeta()->getNumeroTarjeta().$this->getNumerodeuda().self::GEN.$this->getImporte()));
        return $token;
    }

    public function validateCheckout(){
        $valido = false;
        $this->setToken($this->generateToken());
        $tarjetavalida = $this->getServer()->cardValidate($this->getTarjeta()->getNumeroTarjeta());

        $tokenvalido = $this->getServer()->tokenValidate($this->getToken(), $this->getNumerodeuda(), $this->getImporte(), $this->getTarjeta()->getNumeroTarjeta());

        if($tarjetavalida && $tokenvalido)
        {
            $valido = true;
        }
        return $valido;
    }

    public function getIdPago(){
       return $this->getServer()->paymentGenerate();
    }
} 