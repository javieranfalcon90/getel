<?php

namespace App\Controller;

use App\Entity\Tarifa;
use App\Form\TarifaType;
use App\Repository\TarifaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/tarifa")
 */
class TarifaController extends AbstractController
{

    /**
     * @Route("/dataTable", name="tarifa_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio){

        $dql = 'SELECT t FROM App:Tarifa t';

        $columns = array(
            0 => 't.nombre',
            1 => 't.tarifadiurno',
            2 => 't.tarifanocturno'
        );

        $resultados =  $dataTableServicio->datatableResult($request, $dql, $columns);
        $count =  $dataTableServicio->count($request, $dql, $columns);
        $countAll =  $dataTableServicio->countAll($dql);

        $array=array();
        foreach ($resultados as $res){

            $array[]=array(

                $res->getNombre(),
                $res->getDesdediurno()->format('H:i'). ' - ' .$res->getHastadiurno()->format('H:i'),
                $res->getTarifadiurno(),
                $res->getDesdenocturno()->format('H:i'). ' - ' .$res->getHastanocturno()->format('H:i'),
                $res->getTarifanocturno(),
                '
                <div class="text-right">
                <a class="btn btn-link" href="'. $this->generateUrl('tarifa_edit', array('id' => $res->getId())) .'"><i class="fas fa-edit"></i></a>  
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
     * @Route("/", name="tarifa_index", methods={"GET"}, options={"expose" : "true"})
     */
    public function index(TarifaRepository $tarifaRepository): Response
    {
        return $this->render('tarifa/index.html.twig');
    }

    /**
     * @Route("/new", name="tarifa_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $tarifa = new Tarifa();
        $form = $this->createForm(TarifaType::class, $tarifa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tarifa);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('tarifa_index');
        }else{

            $errors = $validator->validate($tarifa);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('tarifa/new.html.twig', [
            'tarifa' => $tarifa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tarifa_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tarifa $tarifa, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(TarifaType::class, $tarifa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'El elemento se ha editado corréctamente'
            );

            return $this->redirectToRoute('tarifa_index');
        }else{

            $errors = $validator->validate($tarifa);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('tarifa/new.html.twig', [
            'tarifa' => $tarifa,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tarifa_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Tarifa $tarifa): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($tarifa);
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
