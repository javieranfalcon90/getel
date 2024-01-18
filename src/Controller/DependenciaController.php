<?php

namespace App\Controller;

use App\Entity\Dependencia;
use App\Form\DependenciaType;
use App\Repository\DependenciaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/dependencia")
 */
class DependenciaController extends AbstractController
{

    /**
     * @Route("/dataTable", name="dependencia_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio){

        $dql = 'SELECT d FROM App:Dependencia d LEFT JOIN d.centro c ';

        $columns = array(
            0 => 'd.nombre',
            1 => 'c.nombre'
        );

        $resultados =  $dataTableServicio->datatableResult($request, $dql, $columns);
        $count =  $dataTableServicio->count($request, $dql, $columns);
        $countAll =  $dataTableServicio->countAll($dql);

        $array=array();
        foreach ($resultados as $res){

            $array[]=array(

                $res->getNombre(),
                $res->getCentro()->getNombre(),
                '
                <div class="text-right">
                <a class="btn btn-link" href="'. $this->generateUrl('dependencia_edit', array('id' => $res->getId())) .'"><i class="fas fa-edit"></i></a>  
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
     * @Route("/", name="dependencia_index", methods={"GET"})
     */
    public function index(DependenciaRepository $dependenciaRepository): Response
    {
        return $this->render('dependencia/index.html.twig');
    }

    /**
     * @Route("/new", name="dependencia_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $dependencia = new Dependencia();
        $form = $this->createForm(DependenciaType::class, $dependencia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($dependencia);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('dependencia_index');
        }else{

            $errors = $validator->validate($dependencia);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('dependencia/new.html.twig', [
            'dependencia' => $dependencia,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="dependencia_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Dependencia $dependencia, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(DependenciaType::class, $dependencia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            //dd($dependencia);


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dependencia_index');
        }else{

            $errors = $validator->validate($dependencia);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('dependencia/new.html.twig', [
            'dependencia' => $dependencia,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="dependencia_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Dependencia $dependencia): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($dependencia);
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
