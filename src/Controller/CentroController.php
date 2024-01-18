<?php

namespace App\Controller;

use App\Entity\Centro;
use App\Form\CentroType;
use App\Repository\CentroRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/centro")
 */
class CentroController extends AbstractController
{

    /**
     * @Route("/dataTable", name="centro_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio){

        $dql = 'SELECT c FROM App:Centro c ';

        $columns = array(
            0 => 'c.nombre'
        );

        $resultados =  $dataTableServicio->datatableResult($request, $dql, $columns);
        $count =  $dataTableServicio->count($request, $dql, $columns);
        $countAll =  $dataTableServicio->countAll($dql);

        $array=array();
        foreach ($resultados as $res){

            $array[]=array(

                $res->getNombre(),
                '
                <div class="text-right">
                <a class="btn btn-link" href="'. $this->generateUrl('centro_edit', array('id' => $res->getId())) .'"><i class="fas fa-edit"></i></a>  
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
     * @Route("/", name="centro_index", methods={"GET"})
     */
    public function index(CentroRepository $centroRepository): Response
    {
        return $this->render('centro/index.html.twig');
    }

    /**
     * @Route("/new", name="centro_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $centro = new Centro();
        $form = $this->createForm(CentroType::class, $centro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($centro);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('centro_index');
        }else{

            $errors = $validator->validate($centro);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('centro/new.html.twig', [
            'centro' => $centro,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="centro_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Centro $centro, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(CentroType::class, $centro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('localidad_index');
        }else{

            $errors = $validator->validate($centro);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('centro/new.html.twig', [
            'centro' => $centro,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="centro_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Centro $centro): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($centro);
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
