<?php 
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        //Comprobamos que estemos entre las 7:00 y las 21:00... sino, no hacemos nada...
        $estoyCurrando = true;// 7 <= intval(date('H')) && intval(date('H')) < 21;
        if($estoyCurrando){ 
            //Dormimos entre 0 minutos y 10 minutos
            $tiempoDormir = 0;// rand(0, 600);
            echo date('YmdHis')." - Dormimos $tiempoDormir \r\n";
            sleep($tiempoDormir);
            echo date('YmdHis')." - Despertamos \r\n";
            $this->controller->importarTickets();
            echo date('YmdHis')." - Categorización terminada \r\n";
        } else {
            echo date('YmdHis')." - No estoy currando... \r\n";
        }

        return 0;
    }
}