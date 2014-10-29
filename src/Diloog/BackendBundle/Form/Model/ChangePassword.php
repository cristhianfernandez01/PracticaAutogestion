<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 19/10/14
 * Time: 12:45
 */

namespace Diloog\BackendBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @Assert\Callback(methods={"passwordCoinciden"})
*/
class ChangePassword
{


    protected $password;


    protected $password2;


    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password2
     */
    public function setPassword2($password2)
    {
        $this->password2 = $password2;
    }

    /**
     * @return mixed
     */
    public function getPassword2()
    {
        return $this->password2;
    }


    public function passwordCoinciden(ExecutionContext $context)
    {
        $password1 = $this->getPassword();
        $password2 = $this->getPassword2();
// Comprobar que coincidan los password
        if ($password1 !== $password2) {
            $context->addViolationAt('password2', 'Las contraseñas no coinciden', array(), null);
            return;
        }

    }

}