<?php 
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\Controller\ServiceDeskController;

class SDImportarCommand extends Command
{
    protected static $defaultName = 'app:sd:importar';
    private $controller;

    public function __construct(ServiceDeskController $controller){
        parent::__construct();
        $this->controller = $controller;
    }

    protected function configure()
    {
        $this->setDescription('Importa los tickets e inserta registro de trabajo en los que proceda.');
        $this->addArgument('run', InputArgument::OPTIONAL, 'Ejecutar ahora?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        if(!empty($input->getArgument('run'))){
            $estoyCurrando = true; 
            $tiempoDormir = 0;
        } else {
            //Comprobamos que estemos entre las 7:00 y las 21:00... sino, no hacemos nada... 
            $estoyCurrando = 7 <= intval(date('H')) && intval(date('H')) < 21 && in_array(date('w'), [1,2,3,4,5]); // Comprobamos si estamos de Lunes a Viernes
            //Dilatamos la ejecución entre 0 y 10 minutos
            $tiempoDormir = rand(0,600);
        }
        
        if($estoyCurrando){ 
            echo date('YmdHis')." - Importar - Dormimos $tiempoDormir \r\n";
            sleep($tiempoDormir);
            echo date('YmdHis')." - Importar - Despertamos \r\n";
            $this->controller->importarTickets();
            echo date('YmdHis')." - Importar - Terminamos \r\n";
        } else {
            echo date('YmdHis')." - Importar - No estoy currando... \r\n";
        }

        return 0;
    }
}