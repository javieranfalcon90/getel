<?php

namespace App\Controller;

use App\Entity\Zona;
use App\Form\ZonaType;
use App\Repository\ZonaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/configuracion/zona")
 */
class ZonaController extends AbstractController
{

    /**
     * @Route("/dataTable", name="zona_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio){

        $dql = 'SELECT z FROM App:Zona z';

        $columns = array(
            0 => 'z.nombre',
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
                <a class="btn btn-link" href="'. $this->generateUrl('zona_edit', array('id' => $res->getId())) .'"><i class="fas fa-edit"></i></a>  
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
     * @Route("/", name="zona_index", methods={"GET"}, options={"expose" : "true"})
     */
    public function index(ZonaRepository $zonaRepository): Response
    {
        return $this->render('zona/index.html.twig');
    }

    /**
     * @Route("/new", name="zona_new", methods={"GET","POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $zona = new Zona();
        $form = $this->createForm(ZonaType::class, $zona);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($zona);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('zona_index');
        }else{

            $errors = $validator->validate($zona);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('zona/new.html.twig', [
            'zona' => $zona,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="zona_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Zona $zona, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(ZonaType::class, $zona);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'El elemento se ha insertado corréctamente'
            );

            return $this->redirectToRoute('zona_index');
        }else{

            $errors = $validator->validate($zona);

            foreach ($errors as $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        return $this->render('zona/new.html.twig', [
            'zona' => $zona,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="zona_delete", methods={"POST"}, options={"expose" : "true"})
     */
    public function delete(Request $request, Zona $zona): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($zona);
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
