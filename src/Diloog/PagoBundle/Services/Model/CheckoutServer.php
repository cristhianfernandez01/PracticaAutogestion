<?php
/**
 * Created by PhpStorm.
 * User: Cristhian
 * Date: 29/10/14
 * Time: 23:04
 */

namespace Diloog\PagoBundle\Services\Model;

use Diloog\AfiliadoBundle\Entity\Tarjeta;

class  CheckoutServer{

   protected $tarjetasaceptadas;

    public function  __construct(){
        $this->tarjetasaceptadas = array('4509953566233704', '5031755734530604', '371180303257522',
                                         '4235647728025682', '5031433215406351','375365153556885');
    }


    /**
     * @param array $tarjetasaceptadas
     */
    public function setTarjetasaceptadas($tarjetasaceptadas)
    {
        $this->tarjetasaceptadas = $tarjetasaceptadas;
    }

    /**
     * @return array
     */
    public function getTarjetasaceptadas()
    {
        return $this->tarjetasaceptadas;
    }


    public function cardValidate($numerotarjeta){
        $valida = false;
        $tamaño = count($this->getTarjetasaceptadas());
       // ladybug_dump($this->getTarjetasaceptadas());
        $aceptadas = $this->getTarjetasaceptadas();
        for ($i=0;$i<$tamaño;$i++){
           // ladybug_dump($aceptadas[$i]);
            if ($aceptadas[$i] == $numerotarjeta){
                $valida = true;
            // ladybug_dump($aceptadas[$i]);
                break;
            }
        }
        return $valida;
    }

    public function tokenValidate($token, $numerodeuda, $importe, $numerotarjeta){
       $valido = false;
       if($token == sha1(md5($numerotarjeta.$numerodeuda.'cgcet2245ydhuz'.$importe))){
         $valido = true;
       }
        return $valido;
    }

    public function paymentGenerate(){
        $idpago = rand(1000000,6000000);
        return $idpago;
    }

} 