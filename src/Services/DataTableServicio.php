<?php

namespace App\Services;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class DataTableServicio
{

    protected $em;

    public function __construct($entityManager)
    {
        $this->em = $entityManager;
    }


    public function datatableResult(Request $request, $dql, $columns, $parameters = []){

        $start = $request->get('start');
        $cant = $request->get('length');
        $search = $request->get('search')['value'];
        $sortColumnId = $request->get('order')[0]['column'];
        $sortDir = $request->get('order')[0]['dir'];
        $sortColumn = $columns[$sortColumnId];

        $cantt = count($columns);

        if($search){

            if(!empty($parameters)){$dql.=" AND (";}else{$dql.= " WHERE (";}

            for ($i = 0; $i < $cantt; $i++) {

                $dql .= $columns[$i] . ' LIKE :search ';

                if ($i != $cantt - 1) {
                    $dql .= 'OR ';
                }
            }

            $dql.= ') ';

        }

        // Seccion de Ordenamiento
        if($sortColumn && $sortDir){
            $dql .= ' ORDER BY '.$sortColumn.' '.$sortDir;
        }

        // Seccion de Creacion del Objeto Query
        $query = $this->em->createQuery($dql);

        $query->setFirstResult($start);
        if ($cant != -1) {
            $query->setMaxResults($cant);
        }


	


        // Seccion de Asignacion de Parametros
        if($search){$query->setParameter('search', '%' . $search . '%');}
        if(!empty($parameters)){
            foreach ($parameters as $key => $value){
                $query->setParameter($key, $value);
            }
        }

        return new Paginator($query);

    }

    public function count(Request $request, $dql, $columns, $parameters = []) {

        $search = $request->get('search')['value'];
        $cantt = count($columns);

        if($search){

            if(!empty($parameters)){$dql.= " AND (";}else{$dql.= " WHERE (";}

            for ($i = 0; $i < $cantt; $i++) {

                $dql .= $columns[$i] . ' LIKE :search ';

                if ($i != $cantt - 1) {
                    $dql .= 'OR ';
                }
            }

            $dql.= ') ';

        }

        $query = $this->em->createQuery($dql);

        if($search){$query->setParameter('search', '%' . $search . '%');}
        if(!empty($parameters)){
            foreach ($parameters as $key => $value){
                $query->setParameter($key, $value);
            }
        }

        return count($query->getResult());
    }

    public function countAll($dql, $parameters = []) {

        $query = $this->em->createQuery($dql);

        if(!empty($parameters)){
            foreach ($parameters as $key => $value){
                $query->setParameter($key, $value);
            }
        }

        return count($query->getResult());
    }





}