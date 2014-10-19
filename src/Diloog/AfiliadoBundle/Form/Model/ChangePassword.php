<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 19/10/14
 * Time: 12:45
 */

namespace Diloog\AfiliadoBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/**
 * @Assert\Callback(methods={"passwordCoinciden"})
*/
class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "La contraseña ingresada no coincide con tu contraseña actual"
     * )
     *
     */
    protected $oldPassword;

    /**
     * @param mixed $newpassword
     */
    public function setNewpassword($newpassword)
    {
        $this->newpassword = $newpassword;
    }

    /**
     * @return mixed
     */
    public function getNewpassword()
    {
        return $this->newpassword;
    }

    /**
     * @param mixed $newpassword2
     */
    public function setNewpassword2($newpassword2)
    {
        $this->newpassword2 = $newpassword2;
    }

    /**
     * @return mixed
     */
    public function getNewpassword2()
    {
        return $this->newpassword2;
    }

    /**
     * @param mixed $oldPassword
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return mixed
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }


    protected $newpassword;


    protected $newpassword2;


    public function passwordCoinciden(ExecutionContext $context)
    {
        $password1 = $this->getNewpassword();
        $password2 = $this->getNewpassword2();
// Comprobar que coincidan los password
        if ($password1 !== $password2) {
            $context->addViolationAt('newpassword2', 'Las contraseñas no coinciden', array(), null);
            return;
        }

    }

}