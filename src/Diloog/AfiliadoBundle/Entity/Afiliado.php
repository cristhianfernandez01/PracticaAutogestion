<?php

namespace Diloog\AfiliadoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Afiliado
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Afiliado
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
     * @ORM\Column(name="numero_afiliado", type="integer")
     */
    private $numeroAfiliado;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=45)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=45)
     */
    private $apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="domicilio", type="string", length=255)
     */
    private $domicilio;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=45)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=25)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="\Diloog\AfiliadoBundle\Entity\Tarjeta", mappedBy="afiliado")
     */

    private $tarjetas;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="\Diloog\PagoBundle\Entity\EstadoDeDeuda", mappedBy="afiliado")
     */
   private $estadosdedeuda;

    /**
     * Get id
     *
     * @return integer 
     */

    public function __construct(){
        $this->tarjetas = new ArrayCollection();
        $this->estadosdedeuda = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set numeroAfiliado
     *
     * @param integer $numeroAfiliado
     * @return Afiliado
     */
    public function setNumeroAfiliado($numeroAfiliado)
    {
        $this->numeroAfiliado = $numeroAfiliado;

        return $this;
    }

    /**
     * Get numeroAfiliado
     *
     * @return integer 
     */
    public function getNumeroAfiliado()
    {
        return $this->numeroAfiliado;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Afiliado
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     * @return Afiliado
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set domicilio
     *
     * @param string $domicilio
     * @return Afiliado
     */
    public function setDomicilio($domicilio)
    {
        $this->domicilio = $domicilio;

        return $this;
    }

    /**
     * Get domicilio
     *
     * @return string 
     */
    public function getDomicilio()
    {
        return $this->domicilio;
    }

    /**
     * Set estado
     *
     * @param string $estado
     * @return Afiliado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Afiliado
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Afiliado
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Afiliado
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Afiliado
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function addTarjeta(\Diloog\AfiliadoBundle\Entity\Tarjeta $tarjeta)
    {
        $this->tarjetas[] = $tarjeta;
    }

    public function setTarjetas(\Doctrine\Common\Collections\ArrayCollection $tarjetas)
    {
        $this->tarjetas=$tarjetas;
    }

    public function getTarjetas()
    {
        return $this->tarjetas;
    }

    public function addEstadoDeDeuda(\Diloog\PagoBundle\Entity\EstadoDeDeuda $estadodedeuda)
    {
        $this->tarjetas[] = $estadodedeuda;
    }

    public function setEstadosDeDeuda(\Doctrine\Common\Collections\ArrayCollection $estadosdedeuda)
    {
        $this->tarjetas=$estadosdedeuda;
    }

    public function getEstadosDeDeuda()
    {
        return $this->estadosdedeuda;
    }

}
