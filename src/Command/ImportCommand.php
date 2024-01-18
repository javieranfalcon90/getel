<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use App\Entity\Llamada;
use App\Entity\Identificador;
use App\Entity\Localidad;

use Doctrine\Persistence\ManagerRegistry;

/**
 * GreetCommand Someone
 *
 * @author jsoto
 */
class ImportCommand extends Command {


    private $doctrine;

    public function __construct(ManagerRegistry $doctrine){
        $this->doctrine = $doctrine;
        parent::__construct();

    }

    protected function configure() {
        $this
                ->setName('fichero:importar')
                ->setDescription('Importar datos de la pizarra')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {

        $em = $this->doctrine->getManager();

        $file = fopen("src\Command\pbx-clean-output-salva22.txt","r") or exit("Unable to open file!");

        //$file = fopen("/opt/pbx-access/log/pbx-clean-output.txt", "r") or exit("Unable to open file!");
	
	//$file = fopen("/opt/pbx-access/log/pbx-clean-output-last.txt", "r") or exit("Unable to open file!");


        while (!feof($file)) {
            
            $arreglo_llamada = fgets($file);

            $output->writeln($arreglo_llamada);

            /*if ($arreglo_llamada[26] == 0) {

                array_splice($arreglo_llamada, 26, 1); // retiro el cero del numero

                array_splice( $arreglo_llamada, 56, 0, " " ); // agrego un espacio en la posicion 56 para correr los elementos

                $output->writeln('encontro elementos con 0');
            }*/


                if (strlen($arreglo_llamada) >= 26 && $arreglo_llamada[26] != 1) {

                    $fecha = (string) $arreglo_llamada[0] . $arreglo_llamada[1] . $arreglo_llamada[2] . $arreglo_llamada[3] . $arreglo_llamada[4] . $arreglo_llamada[5] . $arreglo_llamada[6] . $arreglo_llamada[7] . $arreglo_llamada[8] . $arreglo_llamada[9] . $arreglo_llamada[10] . $arreglo_llamada[11] . $arreglo_llamada[12] . $arreglo_llamada[13];
                    $numero = (string) $arreglo_llamada[19] . $arreglo_llamada[20] . $arreglo_llamada[21];
                    $tronco = (string) $arreglo_llamada[23] . $arreglo_llamada[24];
                    $telefono = (string) $arreglo_llamada[26] . $arreglo_llamada[27] . $arreglo_llamada[28]
                        . $arreglo_llamada[29] . $arreglo_llamada[30] . $arreglo_llamada[31] . $arreglo_llamada[32]
                        . $arreglo_llamada[33] . $arreglo_llamada[34] . $arreglo_llamada[35] . $arreglo_llamada[36]
                        . $arreglo_llamada[37] . $arreglo_llamada[38] . $arreglo_llamada[39] . $arreglo_llamada[40]
                        . $arreglo_llamada[41] . $arreglo_llamada[42] . $arreglo_llamada[43] . $arreglo_llamada[44]
                        . $arreglo_llamada[45] . $arreglo_llamada[46] . $arreglo_llamada[47] . $arreglo_llamada[48]
                        . $arreglo_llamada[49] . $arreglo_llamada[50];
                    $duracion = (string) $arreglo_llamada[57] . $arreglo_llamada[58] . $arreglo_llamada[59]
                        . $arreglo_llamada[60] . $arreglo_llamada[61] . $arreglo_llamada[62] . $arreglo_llamada[63]
                        . $arreglo_llamada[64];

                    /* Obtengo el itentificador asociado a la llamada */
                    if ($arreglo_llamada[18] == '*') {
                        /* Se uso un codigo personal */
                        $identificador = $em->getRepository(Identificador::class)->findOneBy(['numero' => $numero, 'tipo' => 'Cod']);
                    }elseif ($arreglo_llamada[18] != '*') {
                        /* Se uso una extension */
                        $identificador = $em->getRepository(Identificador::class)->findOneBy(['numero' => $numero, 'tipo' => 'Ext']);
                    }

                    /* Obtengo la localidad detino de la llamada */
                    $localidades = $em->getRepository(Localidad::class)->findAll();
                    foreach ($localidades as $l) {
                        $codigo_tamano = strlen($l->getCodigo());
                        $codllamada = substr($telefono, 0, $codigo_tamano);
        
                        if($codllamada == $l->getCodigo()) {
                            $localidad = $l;;  
                        
                            break; 
                        }
                    }


                    //if ((($arreglo_llamada[26] == 0) && (($arreglo_llamada[27] != 7)&&($arreglo_llamada[27] != 1))) || ($arreglo_llamada[26] == 7)) {


                        $llamada = new Llamada();
                  
                        $f = \DateTime::createFromFormat('d/m/y H:i', $fecha);
                        $tim = \DateTime::createFromFormat("H:i's", $duracion);

                        $llamada->setAnno($f->format('Y'));

                        $llamada->setFecha($f);
                        $llamada->setTronco($tronco);
                        $llamada->setTelefono($telefono);
                        $llamada->setDuracion($tim);
                        $llamada->setLocalidad($localidad);
                        $llamada->setIdentificador($identificador);
                
                        /* PARA CALCULAR EL COSTO DE LA LLAMADA HAY QUE CONVERTIR LA DURACION A MINUTOS */        
                        $arreglo_duracion = explode(":", $tim->format('H:i:s'));

                        $duracion_hora = (int) $arreglo_duracion[0];
                        $duracion_min = (int) $arreglo_duracion[1];
                        $duracion_segundos = (int) $arreglo_duracion[2];


                        if ($duracion_segundos > 0) {
                            $duracion_segundoss = 60;
                        } else {
                            $duracion_segundoss = $duracion_segundos;
                        }

                        $duracion_llamada = ($duracion_hora * 3600 + $duracion_min * 60 + $duracion_segundoss) / 60;

                        /* OBTENGO LA TARIFA QUE LE CORRESPONDE COBRAR SEGUN LA HORA DE LA LLAMADA */ 
                        $tarifa = $localidad->getZona()->getTarifa();


                        /* CALCULO EL COSTO DE LA LLAMADA SEGUN LA TARIFA */
                        $hora = \DateTime::createFromFormat('H:i:s', $f->format('H:i:s'));
                        

                            
                        if ($hora >= $tarifa->getDesdediurno() && $hora < $tarifa->getHastadiurno()) {
                            /* calculo tarifa diurna para llamada local o celular */
                            $tarifadiurno = $tarifa->getTarifadiurno();
                            $costo = $duracion_llamada * $tarifadiurno;

                            $t = $tarifadiurno;
                        } else {
                            /* calculo tarifa nocturna para llamada local o celular */
                            $tarifanocturno = $tarifa->getTarifanocturno();
                            $costo = $duracion_llamada * $tarifanocturno;

                            $t = $tarifanocturno;
                        }


                        if($localidad->getCodigo() != '7' && $localidad->getCodigo() != '5'){
                            /*A las llamadas de larga distancia nacional (automática) se les adiciona 0.35 centavos equivalentes al pulso por el establecimiento de la comunicación.*/
                            $costo = $costo + 0.35;
                        }

                        $llamada->setCosto($costo);

                        $em->persist($llamada);
                        $em->flush();


                    /*}*/
                }



            //fwrite($fileb, $llamada . PHP_EOL);
        }

        fclose($file);
        //fclose($fileb);

        return Command::SUCCESS;

    }












}
