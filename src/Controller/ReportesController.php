<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Dependencia;
use App\Entity\Departamento;
use App\Entity\Llamada;
use App\Entity\Identificador;
use App\Entity\Centro;
use Doctrine\Persistence\ManagerRegistry;

class ReportesController extends AbstractController {

    /**
     * @Route("/general", name="reporte_general_index")
     */
    public function general(ManagerRegistry $doctrine): Response {

        $em = $doctrine->getManager();
        $centros = $em->getRepository(Centro::class)->findAll();

        return $this->render('reportes/general.html.twig', [
            'centros' => $centros,
        ]);
    }

    /**
     * @Route("/reporte_general", name="reporte_general", methods={"POST"}, options={"expose" : "true"})
     */
    public function reporte_general(Request $request, ManagerRegistry $doctrine): Response {

        $centro = $request->get('centro');
        $data = [];
        $costoTotal = 0;

        if ($request->get('fecha_inicio') && $request->get('fecha_fin')) {
            $fecha_inicio = new \DateTime($request->get('fecha_inicio'));
            $fecha_fin = new \DateTime($request->get('fecha_fin'));
        }

        $em = $doctrine->getManager();

        $qb = $em->getRepository(Dependencia::class)->createQueryBuilder('dep')
                ->join('dep.centro', 'c')
                ->where('c.nombre = :centro')
                ->setParameter('centro', $centro);

        $dependencias = $qb->getQuery()->getResult();

        $array_dependencia = [];
        foreach ($dependencias as $dependencia) {
            $array_dependencia['nombre'] = $dependencia->getNombre();

            $qb = $em->getRepository(Departamento::class)->createQueryBuilder('dpt')
                    ->join('dpt.dependencia', 'dep')
                    ->where('dep = :dependencia')
                    ->setParameter('dependencia', $dependencia);

            $departamentos = $qb->getQuery()->getResult();

            $array_departamento = [];
            $sum_costo_total = 0;
            $sum_costo_locales = 0;
            $sum_costo_distancia = 0;
            $sum_cant_llamadas = 0;

            $departamento_list = [];

            foreach ($departamentos as $departamento) {
                $array_departamento['nombre'] = $departamento->getNombre();

                $locales = $this->costo_llamadas_locales($departamento, $fecha_inicio, $fecha_fin, $doctrine);
                $distancia = $this->costo_llamadas_distancia($departamento, $fecha_inicio, $fecha_fin, $doctrine);

                $array_departamento['costo_total'] = round(($locales['costo'] + $distancia['costo']), 2);
                $array_departamento['costo_locales'] = round($locales['costo'], 2);
                $array_departamento['costo_distancia'] = round($distancia['costo'], 2);
                $array_departamento['cant_llamadas'] = $locales['cantidad'] + $distancia['cantidad'];

                $sum_costo_total = $sum_costo_total + round(($locales['costo'] + $distancia['costo']), 2);
                $sum_costo_locales = $sum_costo_locales + round($locales['costo'], 2);
                $sum_costo_distancia = $sum_costo_distancia + round($distancia['costo'], 2);
                $sum_cant_llamadas = $sum_cant_llamadas + ($locales['cantidad'] + $distancia['cantidad']);

                $departamento_list[] = $array_departamento;
            }


            $array_dependencia['departamentos'] = $departamento_list;
            $array_dependencia['costo_total'] = round($sum_costo_total, 2);
            $array_dependencia['costo_locales'] = round($sum_costo_locales, 2);
            $array_dependencia['costo_distancia'] = round($sum_costo_distancia, 2);
            $array_dependencia['cant_llamadas'] = $sum_cant_llamadas;

            $data[] = $array_dependencia;

            $costoTotal = $costoTotal + round($sum_costo_total, 2);
        }


        //dump($array_departamento);
        //var_dump($array_gasto_departamento);
        //die();
        $array_result = [
            'centro' => $centro,
            'fecha_inicio' => $fecha_inicio->format('d-m-Y'),
            'fecha_fin' => $fecha_fin->format('d-m-Y'),
            'costo_total' => round($costoTotal, 2),
            'data' => $data
        ];

        $data1 = json_encode($array_result);
        return new Response($data1, 200, array('Content-Type' => 'application/json'));
    }

    public function costo_llamadas_locales($departamento, $fecha_inicio, $fecha_fin, ManagerRegistry $doctrine) {

        $em = $doctrine->getManager();

        $qb = $em->getRepository(Llamada::class)->createQueryBuilder('ll')
                ->join('ll.identificador', 'i')
                ->join('i.departamento', 'dpt')
                ->join('ll.localidad', 'l')
                ->where('dpt = :departamento')
                ->andWhere('(ll.fecha BETWEEN :fecha_inicio AND :fecha_fin) ')
                ->andWhere('l.codigo = :codigo')
                ->setParameter('departamento', $departamento)
                ->setParameter('fecha_inicio', $fecha_inicio)
                ->setParameter('fecha_fin', $fecha_fin)
                ->setParameter('codigo', '7');

        $llamadas = $qb->getQuery()->getResult();

        $costo = 0;
        foreach ($llamadas as $ll) {
            $costo = $costo + $ll->getCosto();
        }


        return array('costo' => $costo, 'cantidad' => count($llamadas));
    }

    public function costo_llamadas_distancia($departamento, $fecha_inicio, $fecha_fin, ManagerRegistry $doctrine) {

        $em = $doctrine->getManager();

        $qb = $em->getRepository(Llamada::class)->createQueryBuilder('ll')
                ->join('ll.identificador', 'i')
                ->join('i.departamento', 'dpt')
                ->join('ll.localidad', 'l')
                ->where('dpt = :departamento')
                ->andWhere('(ll.fecha BETWEEN :fecha_inicio AND :fecha_fin) ')
                ->andWhere('l.codigo <> :codigo')
                ->setParameter('departamento', $departamento)
                ->setParameter('fecha_inicio', $fecha_inicio)
                ->setParameter('fecha_fin', $fecha_fin)
                ->setParameter('codigo', '7');

        $llamadas = $qb->getQuery()->getResult();

        $costo = 0;
        foreach ($llamadas as $ll) {
            $costo = $costo + $ll->getCosto();
        }

        return array('costo' => $costo, 'cantidad' => count($llamadas));
    }

    /**
     * @Route("/codigo", name="reporte_codigo_index")
     */
    public function codigo(ManagerRegistry $doctrine): Response {

        $em = $doctrine->getManager();
        $identificadores = $em->getRepository(Identificador::class)->findBy(['tipo' => "Cod"]);

        return $this->render('reportes/codigo.html.twig', [
            'identificadores' => $identificadores,
        ]);
    }
    
        /**
     * @Route("/reporte_codigo", name="reporte_codigo", methods={"POST"}, options={"expose" : "true"})
     */
    public function reporte_codigo(Request $request, ManagerRegistry $doctrine): Response {

        $codigo = $request->get('codigo');
        $centro = '';
        $data = [];
        $costoTotal = 0;

        if ($request->get('fecha_inicio') && $request->get('fecha_fin')) {
            $fecha_inicio = new \DateTime($request->get('fecha_inicio'));
            $fecha_fin = new \DateTime($request->get('fecha_fin'));
        }

        $em = $doctrine->getManager();
        

        $qb = $em->getRepository(Llamada::class)->createQueryBuilder('ll')
                ->join('ll.identificador', 'i')
                ->where('i.numero = :codigo')
                ->andWhere('(ll.fecha BETWEEN :fecha_inicio AND :fecha_fin) ')
                ->setParameter('fecha_inicio', $fecha_inicio)
                ->setParameter('fecha_fin', $fecha_fin)
                ->setParameter('codigo', $codigo);

        $llamadas = $qb->getQuery()->getResult();
        

        if($llamadas){
           $centro = $llamadas[0]->getIdentificador()->getDepartamento()->getDependencia()->getCentro()->getNombre();
           $codigo = $llamadas[0]->getIdentificador()->getNumero();
        }
        
        $array_llamada = [];
        $costoTotal = 0;
        foreach ($llamadas as $llamada){
            $array_llamada['fecha'] = $llamada->getFecha()->format('d-m-Y');
            $array_llamada['telefono'] = $llamada->getTelefono();
            $array_llamada['duracion'] = $llamada->getDuracion()->format('H:i:s');
            $array_llamada['localidad'] = $llamada->getLocalidad()->getNombre();
            $array_llamada['costo'] = $llamada->getCosto();
            
            $costoTotal = $costoTotal + $llamada->getCosto();
            
            $data[] = $array_llamada;
        }

        $array_result = [
            'centro' => $centro,
            'codigo' => $codigo,
            'fecha_inicio' => $fecha_inicio->format('d-m-Y'),
            'fecha_fin' => $fecha_fin->format('d-m-Y'),
            'costo_total' => round($costoTotal, 2),
            'data' => $data
        ];

        $data1 = json_encode($array_result);
        return new Response($data1, 200, array('Content-Type' => 'application/json'));
    }

}
