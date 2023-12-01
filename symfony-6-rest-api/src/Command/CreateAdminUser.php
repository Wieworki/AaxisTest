<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'new:admin:user',
    description: 'Creates a new admin user',
)]
class CreateAdminUser extends Command
{
    private EntityManagerInterface $doctrine;
    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $doctrine, UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
        $this->doctrine = $doctrine;
        $this->hasher = $hasher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Admin email')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = new User();
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $hashedPassword = $this->hasher->hashPassword(
            $user,
            $password
        );

        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setRoles(["admin"]);

        $this->doctrine->persist($user);
        $this->doctrine->flush();

        if ($email) {
            $io->note(sprintf('You passed an argument: %s', $email));
        }

        $io->success('The user has been created');

        return Command::SUCCESS;
    }
}
