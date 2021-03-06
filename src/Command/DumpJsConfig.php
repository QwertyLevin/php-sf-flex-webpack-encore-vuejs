<?php
namespace App\Command;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class DumpJsConfig extends ContainerAwareCommand
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $csrfTokenParameter;

    /**
     * @var string
     */
    protected $apiPlatformPrefix;

    public function __construct(string $csrfTokenParameter, string $apiPlatformPrefix, \Twig_Environment $twig)
    {
        $this->twig = $twig;
        $this->csrfTokenParameter = $csrfTokenParameter;
        $this->apiPlatformPrefix = $apiPlatformPrefix;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:dump-js-config')
            ->setDescription('Create the config.js file.')
            ->setHelp('Dump symfony configuration into a config.js file available from assets/js/*')
            ->addArgument('host', InputArgument::OPTIONAL, 'The full hostname of the web-server.', 'localhost')
            ->addArgument('port', InputArgument::OPTIONAL, 'The port for the web-server.', '80')
            ->addArgument('quasarStyle', InputArgument::OPTIONAL, 'The style for quasar framework: mat or ios.', 'mat');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = $this->getEnv();
        $host = $input->getArgument('host');
        $port = $input->getArgument('port');
        $quasarStyle = $input->getArgument('quasarStyle');

        $validator = Validation::createValidator();
        $violations['port'] = $validator->validate($port, [
            new Assert\Type(['type' => 'numeric', ]),
        ]);

        $violations['quasarStyle'] = $validator->validate($quasarStyle, [
            new Assert\Choice(['choices' => ['mat', 'ios'], ]),
        ]);

        if (0 !== count($violations['port']) && 0 !== count($violations['quasarstyle'])) {
            $output->writeln([
                'Params errors',
                '=======================', ]);
            foreach ($violations as $paramName => $violation) {
                foreach($violation as $v) {
                    $output->writeln(ucfirst($paramName) . ': ' . $v->getMessage());
                }
            }

            return;
        }

        $output->writeln([
            'Js config file creation',
            '=======================',
            'arguments:',
            'host:port = ' . $host . ':' . $port,
            'quasarStyle = ' . $quasarStyle,
        ]);

        $content = $this->twig->render('command/config.js.twig', [
            'env' => $env,
            'host' => trim($host),
            'port' => trim($port),
            'csrfTokenParameter' => $this->csrfTokenParameter,
            'apiPlatformPrefix' => $this->apiPlatformPrefix,
            'quasarStyle' => $quasarStyle,
        ]);

        $output->writeln([
            'File content',
            '============',
            $content,
        ]);

        $helper = $this->getHelper('question');
        $projectDir = $this->getContainer()->get('kernel')->getRootDir() . '/..';
        $configFilepath = '/assets/js/lib/config.js';
        $filepath = $projectDir . $configFilepath;

        if (file_exists($filepath)) {
            $question = new ConfirmationQuestion($configFilepath . ' file already exists, confirm it`s replacement (y or n, default n) ?', false);
            $response = $helper->ask($input, $output, $question);
            if (!$response) {
                $output->writeln([
                    $response,
                    'Process stopped',
                    'New file not saved',
                ]);

                return;
            }
        }

        $res = file_put_contents($filepath, $content);

        if (!$res) {
            $output->writeln([
                'Creation file failed',
            ]);
        } else {
            $output->writeln([
                'File created at ' . $configFilepath,
            ]);
        }
    }

    protected function getEnv(): string
    {
        $systemEnv = getenv();
        $env = 'dev'; // default
        if (array_key_exists('APP_ENV', $systemEnv)) {
            $env = $systemEnv['APP_ENV'];
        }

        return $env;
    }
}
