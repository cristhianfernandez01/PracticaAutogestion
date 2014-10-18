<?php

namespace Diloog\AfiliadoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Diloog\AfiliadoBundle\Entity\Tarjeta;
use Diloog\AfiliadoBundle\Form\TarjetaType;

/**
 * Tarjeta controller.
 *
 */
class TarjetaController extends Controller
{

    /**
     * Lists all Tarjeta entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AfiliadoBundle:Tarjeta')->findAll();

        return $this->render('AfiliadoBundle:Tarjeta:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Tarjeta entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Tarjeta();
        $entity->setAfiliado($this->getUser());
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tarjeta'));
        }

        return $this->render('AfiliadoBundle:Tarjeta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Tarjeta entity.
     *
     * @param Tarjeta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Tarjeta $entity)
    {
        $form = $this->createForm(new TarjetaType(), $entity, array(
            'action' => $this->generateUrl('tarjeta_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Tarjeta entity.
     *
     */
    public function newAction()
    {
        $entity = new Tarjeta();
        $form   = $this->createCreateForm($entity);

        return $this->render('AfiliadoBundle:Tarjeta:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Tarjeta entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AfiliadoBundle:Tarjeta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarjeta entity.');
        }


        return $this->render('AfiliadoBundle:Tarjeta:show.html.twig', array(
            'entity'      => $entity
        ));
    }

    /**
     * Displays a form to edit an existing Tarjeta entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AfiliadoBundle:Tarjeta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarjeta entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AfiliadoBundle:Tarjeta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Tarjeta entity.
    *
    * @param Tarjeta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Tarjeta $entity)
    {
        $form = $this->createForm(new TarjetaType(), $entity, array(
            'action' => $this->generateUrl('tarjeta_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Modificar datos', 'attr' => array('class' => 'btn btn-sm btn-default')));

        return $form;
    }
    /**
     * Edits an existing Tarjeta entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AfiliadoBundle:Tarjeta')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tarjeta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tarjeta'));
        }

        return $this->render('AfiliadoBundle:Tarjeta:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Tarjeta entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AfiliadoBundle:Tarjeta')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tarjeta entity.');
            }

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('tarjeta'));
    }

    /**
     * Creates a form to delete a Tarjeta entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tarjeta_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Borrar tarjeta', 'attr' => array('class' => 'btn btn-sm btn-default')))
            ->getForm()

        ;
    }
}
