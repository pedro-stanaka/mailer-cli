<?php
namespace Flock\Console\Command;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Yaml\Yaml;


class MailerCommand extends Command {

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('notification:mailer')
            ->setDescription('send an email to someone.')
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'What is the e-mail address of the receiver?'
            )
            ->addArgument(
                'body',
                InputArgument::REQUIRED,
                'Information about the machine and dataset in which the experiment was running.'
            )
            ->addArgument(
                'subject',
                InputArgument::OPTIONAL,
                'What is the subject of the conversation?'
            );
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $subject = $input->getArgument('subject') ? $input->getArgument('subject') : "Sent by flock-mailer";
        $body = $input->getArgument('body');

        $this->output = $output;

        $mailer = new \PHPMailer;

        $this->configureMailer($mailer);

        $this->sendMail($mailer, $email, $subject, $body);

    }

    /**
     * @param $mailer \PHPMailer
     */
    private function configureMailer($mailer)
    {

        $configFile = ROOT . '/config/email.yml';
        $configValues = [];

        try {
            $configValues = Yaml::parse($configFile);
        } catch (Exception $e) {
            $this->output->writeln("There was a error while parsing the configuration file. Check the error below:");
            $this->output->writeln($e->getMessage());
            exit(1);
        }

        if (!empty($configValues)) {
            $mailer->isSMTP();                                      // Set mailer to use SMTP
            $mailer->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
            $mailer->Port = 587;                                    // TCP port to connect to
            $mailer->SMTPAuth = true;                               // Enable SMTP authentication
            $mailer->Username = $configValues['from'];              // SMTP username
            $mailer->Password = $configValues['passwd'];            // SMTP password
            $mailer->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mailer->setFrom($configValues['from'], $configValues['name']);
            if ($configValues['debug'] && $configValues['debug'] > 0) {
                $mailer->SMTPDebug = 2;
                $mailer->Debugoutput = 'html';
            }
        }
    }

    /**
     * @param $mailer \PHPMailer
     * @param $to string
     * @param $subject string
     * @param $body string
     */
    private function sendMail($mailer, $to, $subject, $body)
    {
        $mailer->addAddress($to);
        $mailer->Subject = $subject;

        $loader = new FilesystemLoader(ROOT . '/src/Flock/Templates/%name%');


        $templating = new PhpEngine(new TemplateNameParser(), $loader);
        $mailer->msgHTML($templating->render('message.php', array('custom' => $body)));

        if ( $mailer->send() ) {
            echo "Message sent!";
        } else {
            echo "The message could not be delivered.";
            echo "Mailer error: " . $mailer->ErrorInfo;
        }

    }


}