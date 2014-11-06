<?php

namespace Diloog\BackendBundle\Controller;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Diloog\BackendBundle\Filter\OperacionFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Sftp as SftpAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Diloog\BackendBundle\Form\Model\ChangePassword;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $name = 'Mundo';
        return $this->render('BackendBundle:Default:index.html.twig', array('name' => $name));
    }

    public function loginAction(){
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
        $error = $peticion->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR,
            $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );
        return $this->render('BackendBundle::login.html.twig', array(
            'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
            'error' => $error
        ));

    }


    public  function envioAction(){
      $sftp = $this->get('diloog_backend.sftp');
      $adapter = new SftpAdapter($sftp,'/pagos/');
      $filesystem = new Filesystem($adapter);
      //ld($filesystem);
      $content = 'Hello I am the new content';
      $filesystem->write('archivo.txt', $content);
      return new Response('<html><head><title>Prueba SFTP</title></head><body><h1>Esto es una prueba para SFTP</h1></body></html>');
    }

    public function pruebaExcelAction(){
        $factory= $this->get('phpexcel');
       $phpexcel = $factory->createPHPExcelObject('Archivo');
       // $writer = $factory->createWriter($phpexcel,'CSV');
        $writer = $this->get('phpexcel')->createWriter($phpexcel, 'CSV')
            ->setDelimiter(',')
            ->setEnclosure('"')
            ->setLineEnding("\r\n")
            ->setSheetIndex(0)
            ->save(str_replace('.php', '.csv', __FILE__));
    }

    public function listarAfiliadoAction(){
        $source = new Entity('AfiliadoBundle:Afiliado');

        // Get a grid instance
        $grid = $this->get('grid');

        // Attach the source to the grid
        $grid->setSource($source);
        $columnIds = array('alias','email');
        $grid->hideColumns($columnIds);
        // Configuration of the grid

        // Manage the grid redirection, exports and the response of the controller
        return $grid->getGridResponse('@Backend/Default/listaafiliados.html.twig');
    }

    public function listarUsuarioAction(){
        $source = new Entity('AfiliadoBundle:Afiliado');

        $grid = $this->get('grid');

        $grid->setSource($source);


        $columns = array( $grid->getColumn('id'), $grid->getColumn('numeroAfiliado') ,$grid->getColumn('apellido'),$grid->getColumn('nombre'), $grid->getColumn('alias') );
        $columnsid = array('id', 'numeroAfiliado', 'nombre', 'apellido', 'alias','email');
        $grid->setVisibleColumns($columnsid);
        $rowAction = new RowAction('Cambiar password', 'cambiar_password_usuarios', true, '_self', array('class' => 'btn btn-default'));
        $rowAction->setRouteParameters(array('id'));
        $rowAction->setConfirmMessage('¿Desea modificar el password de este usuario?');
        $grid->addRowAction($rowAction);


        return $grid->getGridResponse('@Backend/Default/listausuarios.html.twig');
    }


    public function cambiarPasswordAction(Request $request,$id){
        $cambioPassword = new ChangePassword();
        $form = $this->createFormBuilder($cambioPassword)
            ->add('password', 'password')
            ->add('password2', 'password')
            ->add('aceptar', 'submit')
            ->getForm();
        $idafiliado = (int)$id;
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $cambioPassword = $form->getData();
            $afiliado = $em->getRepository('AfiliadoBundle:Afiliado')->findOneBy(array('id' => $idafiliado));
          //  ld($afiliado);
            $afiliado->setPassword($cambioPassword->getPassword());
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'notice',
                'Se ha cambiado exitosamente la contraseña.'
            );

            return $this->redirect($this->generateUrl('listar_usuarios'));
        }

        return $this->render('BackendBundle:Default:cambiopasswordafiliado.html.twig', array('form' => $form->createView()));

    }

    public function operacionFilterAction()
    {
        $form = $this->get('form.factory')->create(new OperacionFilterType());

        if ($this->get('request')->getMethod() == 'POST') {
            $form->submit($this->get('request'));

            $queryBuilder = $this->get('doctrine.orm.entity_manager')
                ->getRepository('BackendBundle:Operacion')
                ->createQueryBuilder('e');


            $this->get('lexik_form_filter.query_builder_updater')
                ->addFilterConditions($form, $queryBuilder);
        }

        return $this->render('BackendBundle:Default:listaoperaciones.html.twig', array(
            'form' => $form->createView(),
        ));
    }


}
