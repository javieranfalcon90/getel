<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Llamada;
use App\Entity\Localidad;
use Doctrine\Persistence\ManagerRegistry;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(ManagerRegistry $doctrine): Response
    {

        $em = $doctrine->getManager();

        $anno = (new \DateTime('now'))->format('Y');

        $llamadas = $em->getRepository(Llamada::class)->findBy(['anno' => $anno]);
                
        $costo_total_distancia = 0;
        $costo_total_local = 0;
        foreach ($llamadas as $ll) {

            if($ll->getLocalidad()->getCodigo() == '7'){
                $costo_total_local = $costo_total_local + $ll->getCosto();
            }else{
                $costo_total_distancia = $costo_total_distancia +$ll->getCosto();
            }


        }

        $costo_total = $costo_total_local + $costo_total_distancia;


        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'costo_total' => round($costo_total, 2),
            'costo_total_local' => round($costo_total_local, 2),
            'costo_total_distancia' => round($costo_total_distancia, 2),

            'llamadas_x_localidades' => $this->llamadas_x_localidades($em)
        ]);
    }


    public function llamadas_x_localidades($em){

        $anno = (new \DateTime('now'))->format('Y');

        $localidades = $em->getRepository(Localidad::class)->findAll();

        $array_llamadas_x_localidades = [];

        foreach ($localidades as $localidad){

            $array_llamadas_x_localidades['localidad'][] = $localidad->getNombre();

            $qb = $em->getRepository(Llamada::class)->createQueryBuilder('ll')
                ->join('ll.localidad', 'l')
                ->where('ll.anno = :anno')
                ->andWhere('l = :localidad')
                ->setParameter('anno', $anno)
                ->setParameter('localidad', $localidad);

            $llamadas = $qb->getQuery()->getResult();

            $array_llamadas_x_localidades['count'][] = count($llamadas);

        }

        return $array_llamadas_x_localidades;

    }
        /**
     * @Route("/configuracion", name="configuracion")
     */
    public function configuracion(): Response
    {
        return $this->render('main/configuracion.html.twig');
    }
}
