<?php

namespace App\Controller;

use App\Entity\Identificador;
use App\Form\IdentificadorType;
use App\Repository\IdentificadorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/identificador")
 */
class IdentificadorController extends AbstractController
{

    /**
     * @Route("/dataTable", name="identificador_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio){

        $dql = 'SELECT i FROM App:Identificador i LEFT JOIN i.departamento d LEFT JOIN d.dependencia dp LEFT JOIN dp.centro c ';

        $columns = array(
            0 => 'i.numero',
            1 => 'i.tipo',
            2 => 'i.responsable',
    	    3 => 'd.nombre',
    	    4 => 'dp.nombre',
    	    5 => 'c.nombre'
        );

        $resultados =  $dataTableServicio->datatableResult($request, $dql, $columns);
        $count =  $dataTableServicio->count($request, $dql, $columns);
        $countAll =  $dataTableServicio->countAll($dql);

        $array=array();
        foreach ($resultados as $res){

            $array[]=array(

                $res->getNumero(),
                $res->getTipo(),
                ($res->getResponsable()) ? $res->getResponsable() : '-',
                ($res->getDepartamento()) ? $res->getDepartamento()->getNombre() : '-',
                ($res->getDepartamento()) ? $res->getDepartamento()->getDependencia()->getNombre() : '-',
                ($res->getDepartamento()) ? $res->getDepartamento()->getDependencia()->getCentro()->getNombre() : '-',
                '
                <div class="text-right">
                <a class="btn btn-link" href="'. $this->generateUrl('identificador_edit', array('id' => $res->getId())) .'"><i class="fas fa-edit"></i></a>  
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
     * @Route("/", name="identificador_index", methods={"GET"})
     */
    public function index(IdentificadorRepository $identificadorRepository): Response
    {
        return $this->render('identificador/index.html.twig');
    }

    /**
     * @Route("/new", name="identificador_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $identificador = new Identificador();
        $form = $this->createForm(IdentificadorType::class, $identificador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($identificador);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('identificador_index');
        }else{

            $errors = $validator->validate($identificador);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('identificador/new.html.twig', [
            'identificador' => $identificador,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="identificador_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Identificador $identificador, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(IdentificadorType::class, $identificador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('identificador_index');
        }else{

            $errors = $validator->validate($identificador);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('identificador/new.html.twig', [
            'identificador' => $identificador,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="identificador_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Identificador $identificador): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($identificador);
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
