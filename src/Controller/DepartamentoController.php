<?php

namespace App\Controller;

use App\Entity\Departamento;
use App\Form\DepartamentoType;
use App\Repository\DepartamentoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/departamento")
 */
class DepartamentoController extends AbstractController
{

    /**
     * @Route("/dataTable", name="departamento_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio){

        $dql = 'SELECT d FROM App:Departamento d LEFT JOIN d.dependencia dep LEFT JOIN dep.centro c ';

        $columns = array(
            0 => 'd.nombre',
            1 => 'dep.nombre',
            2 => 'c.nombre'
        );

        $resultados =  $dataTableServicio->datatableResult($request, $dql, $columns);
        $count =  $dataTableServicio->count($request, $dql, $columns);
        $countAll =  $dataTableServicio->countAll($dql);

        $array=array();
        foreach ($resultados as $res){

            $array[]=array(

                $res->getNombre(),
                ($res->getDependencia()) ? $res->getDependencia()->getNombre() : '-',
                ($res->getDependencia()) ? $res->getDependencia()->getCentro()->getNombre() : '-',
                '
                <div class="text-right">
                <a class="btn btn-link" href="'. $this->generateUrl('departamento_edit', array('id' => $res->getId())) .'"><i class="fas fa-edit"></i></a>  
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
     * @Route("/", name="departamento_index", methods={"GET"})
     */
    public function index(DepartamentoRepository $departamentoRepository): Response
    {
        return $this->render('departamento/index.html.twig');
    }

    /**
     * @Route("/new", name="departamento_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $departamento = new Departamento();
        $form = $this->createForm(DepartamentoType::class, $departamento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($departamento);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('departamento_index');
        }else{

            $errors = $validator->validate($departamento);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('departamento/new.html.twig', [
            'departamento' => $departamento,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="departamento_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Departamento $departamento, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(DepartamentoType::class, $departamento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('departamento_index');
        }else{

            $errors = $validator->validate($departamento);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('departamento/new.html.twig', [
            'departamento' => $departamento,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="departamento_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Departamento $departamento): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($departamento);
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
