<?php 
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\Controller\AdobeConnectController;

class AdobeConnectCommand extends Command
{
    protected static $defaultName = 'app:adobe:comprobar';
    private $controller;

    public function __construct(AdobeConnectController $controller){
        parent::__construct();
        $this->controller = $controller;
    }

    protected function configure()
    {
        $this->setDescription('Actualiza los detalles de los tickets.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $infoEnlaces = $this->controller->infoReuniones();

        return 0;
    }
}