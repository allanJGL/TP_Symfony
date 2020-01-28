<?php

namespace App\Command;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';


    private $entityManager;
    private $passwordEncoder;

    public function __construct(string $name = null, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setDescription('This command create an admin user')
            ->addArgument('email', InputArgument::REQUIRED, 'Admin user email')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin user password')
            ->addArgument('firstName', InputArgument::OPTIONAL, 'First Name')
            ->addArgument('lastName', InputArgument::OPTIONAL, 'Last Name')
            ->addArgument('birthdate', InputArgument::OPTIONAL, 'Birthdate');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            'Create user ?',
            false, '/^(y|j)/i');

        if (!$helper->ask($input, $output, $question)) {
            return 0;
        }

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $birthdate = $input->getArgument('birthdate');

        $io->note(sprintf('User email: %s', $email));
        $io->note(sprintf('User password: %s', $password));
        $io->note(sprintf('User First Name: %s', $firstName ?? ''));
        $io->note(sprintf('User Last Name: %s', $lastName ?? ''));
        $io->note(sprintf('User Birthdate: %s', $birthdate ?? ''));

        $user = new User();
        $hashedPassword = $this->passwordEncoder->encodePassword($user, $password);
        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setFirstName($firstName ?? '');
        $user->setLastName($lastName ?? '');
        $user->setBirthdate($birthdate);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            $io->error('A error occured : ' . $exception->getMessage());

            return 0;
        }

        $io->success('A new admin has been created');

        return 0;
    }
}