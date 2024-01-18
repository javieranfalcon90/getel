<?php

namespace App\Controller;

use App\Entity\Llamada;
use App\Form\LlamadaType;
use App\Repository\LlamadaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\DataTableServicio;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/llamada")
 */
class LlamadaController extends AbstractController
{

    /**
     * @Route("/dataTable", name="llamada_dataTable", methods={"GET"}, options={"expose" : "true"})
     */
    public function dataTableAction(Request $request, DataTableServicio $dataTableServicio){

        $parameters['anno'] = $request->get('anno');


        $dql = 'SELECT ll FROM App:Llamada ll JOIN ll.localidad l LEFT JOIN ll.identificador i LEFT JOIN i.departamento dpt LEFT JOIN dpt.usuarios u LEFT JOIN dpt.dependencia dep LEFT JOIN dep.centro c WHERE ll.anno = :anno ';

        $columns = array(
            0 => 'll.fecha',
            1 => 'll.tronco',
            2 => 'll.telefono',
            3 => 'll.duracion',
            4 => 'i.numero',
            5 => 'l.nombre'
        );


        if($this->isGranted(['ROLE_J.CENTRO'])){
            $dql .= 'AND WHERE c = :centro ';

            $parameters['centro'] = $this->getUser()->getDepartamento()->getDependencia()->getCentro();
        }
        if($this->isGranted(['ROLE_J.DEPENDENCIA'])){
            $dql .= 'AND WHERE dep = :dependencia ';

            $parameters['dependencia'] = $this->getUser()->getDepartamento()->getDependencia();
        }
        if($this->isGranted(['ROLE_J.DEPARTAMENTO'])){
            $dql .= 'AND WHERE dpt = :departamento ';

            $parameters['departamento'] = $this->getUser()->getDepartamento();
        }
        if($this->isGranted(['ROLE_J.USUARIO'])){
            $dql .= 'AND WHERE u = :usuario ';

            $parameters['usuario'] = $this->getUser();
        }

        $resultados =  $dataTableServicio->datatableResult($request, $dql, $columns, $parameters);
        $count =  $dataTableServicio->count($request, $dql, $columns, $parameters);
        $countAll =  $dataTableServicio->countAll($dql, $parameters);

        $array=array();
        foreach ($resultados as $res){

            $array[]=array(

                $res->getFecha()->format('d/m/Y h:i A'),
                $res->getTronco(),
                $res->getTelefono(),
                $res->getDuracion()->format('H:i:s'),
                ($res->getIdentificador()) ? $res->getIdentificador()->getTipo().' '.$res->getIdentificador()->getNumero() : '-',
                $res->getLocalidad()->getNombre(),
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
     * @Route("/", name="llamada_index", methods={"GET"})
     */
    public function index(LlamadaRepository $llamadaRepository): Response
    {

        $anno = new \DateTime('now');
        
        return $this->render('llamada/index.html.twig', [
            'anno' => $anno->format('Y'),
        ]);
    }

}
