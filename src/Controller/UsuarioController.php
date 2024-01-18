<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\UsuarioType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/configuracion/usuario")
 */
class UsuarioController extends AbstractController
{

    /**
     * @Route("/dataTable", name="usuario_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio){

        $dql = 'SELECT u FROM App:Usuario u LEFT JOIN u.departamento d ';

        $columns = array(
            0 => 'u.nombre',
            1 => 'u.email',
			2 => 'u.username',
            3 => 'u.role',
            4 => 'd.nombre'
        );

        $resultados =  $dataTableServicio->datatableResult($request, $dql, $columns);
        $count =  $dataTableServicio->count($request, $dql, $columns);
        $countAll =  $dataTableServicio->countAll($dql);

        $array=array();
        foreach ($resultados as $res){

            $array[]=array(

                $res->getNombre(),
                ($res->getEmail()) ? $res->getEmail() : '<div class="h6"><span class="badge badge-dark">na</span></div>',
                ($res->getUsername()) ? $res->getUsername() : '<div class="h6"><span class="badge badge-dark">na</span></div>',
                $res->getRole(),
                ($res->getEstado() == true) ? '<div class="h6"><span class="badge badge-success"><i class="fas fa-check"></i> </span></div>' : '<div class="h6"><span class="badge badge-danger"><i class="fas fa-times"></i> </span></div>',
                ($res->getDepartamento()) ? $res->getDepartamento()->getNombre() : '',

                '
                <div class="text-right">
                <a class="btn btn-link" href="'. $this->generateUrl('usuario_edit', array('id' => $res->getId())) .'"><i class="fas fa-edit"></i></a>  
                <a class="btn btn-link eliminar"  href="javascript:;" id="' . $res->getId() . '"><i class="fas fa-trash"></i></a>
                </div>
                '
            );
        }

        $data = array(
            "iTotalRecords"=> $countAll,//consulta para el total de elementos
            "iTotalDisplayRecords"=> $count,//consulta para el filtro de elementos
            "data" => $array
        );

        $data1 = json_encode($data);
        return new Response($data1, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * @Route("/", name="usuario_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('usuario/index.html.twig');
    }

    /**
     * @Route("/new", name="usuario_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator, UserPasswordHasherInterface $encoder): Response
    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $usuario->setSalt(sha1(time()));
            if($usuario->getPassword()){
                $passwordCodificado = $encoder->hashPassword($usuario, $usuario->getPassword());
                $usuario->setPassword($passwordCodificado);
            }else{
                $usuario->setPassword(null);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('usuario_index');
        }else{

            $errors = $validator->validate($usuario);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="usuario_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Usuario $usuario, ValidatorInterface $validator, UserPasswordHasherInterface $encoder): Response
    {
        $password_actual = $usuario->getPassword();

        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($usuario->getPassword() == null) {
                $usuario->setPassword($password_actual);
            } else {
                $passwordCodificado = $encoder->hashPassword($usuario, $usuario->getPassword());
                $usuario->setPassword($passwordCodificado);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('usuario_index');
        }else{

            $errors = $validator->validate($usuario);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="usuario_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Usuario $usuario): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($usuario);
            $em->flush();

            $this->addFlash(
                'success',
                'El elemento se ha eliminado corréctamente'
            );

        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                'No se pudo eliminar elemento seleccionado, ya que puede estar siendo usado'
            );

        }

        return new Response(null, "200", array('Content-Type' => 'application/json'));
    }
}
