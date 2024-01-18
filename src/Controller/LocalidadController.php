<?php

namespace App\Controller;

use App\Entity\Localidad;
use App\Form\LocalidadType;
use App\Repository\LocalidadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/localidad")
 */
class LocalidadController extends AbstractController
{

    /**
     * @Route("/dataTable", name="localidad_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio){

        $dql = 'SELECT l FROM App:Localidad l JOIN l.zona z ';

        $columns = array(
            0 => 'l.nombre',
            1 => 'l.codigo',
            2 => 'l.inicial',
            3 => 'z.nombre'
        );

        $resultados =  $dataTableServicio->datatableResult($request, $dql, $columns);
        $count =  $dataTableServicio->count($request, $dql, $columns);
        $countAll =  $dataTableServicio->countAll($dql);

        $array=array();
        foreach ($resultados as $res){

            $array[]=array(

                $res->getNombre(),
                $res->getCodigo(),
                $res->getInicial(),
                $res->getZona()->getNombre(),
                '
                <div class="text-right">
                <a class="btn btn-link" href="'. $this->generateUrl('localidad_edit', array('id' => $res->getId())) .'"><i class="fas fa-edit"></i></a>  
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
     * @Route("/", name="localidad_index", methods={"GET"})
     */
    public function index(LocalidadRepository $localidadRepository): Response
    {
        return $this->render('localidad/index.html.twig');
    }

    /**
     * @Route("/new", name="localidad_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $localidad = new Localidad();
        $form = $this->createForm(LocalidadType::class, $localidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($localidad);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('localidad_index');
        }else{

            $errors = $validator->validate($localidad);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('localidad/new.html.twig', [
            'localidad' => $localidad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="localidad_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Localidad $localidad, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(LocalidadType::class, $localidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('localidad_index');
        }else{

            $errors = $validator->validate($localidad);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('localidad/new.html.twig', [
            'localidad' => $localidad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="localidad_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Localidad $localidad): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($localidad);
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
