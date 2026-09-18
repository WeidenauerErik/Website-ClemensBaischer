<?php

namespace App\Command;

use App\Entity\AdminUser;
use App\Repository\AdminUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:admin:create', description: 'Create an admin user (e.g. to bootstrap the first login)')]
final class CreateAdminUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdminUserRepository $adminUsers,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('firstName', InputArgument::REQUIRED)
            ->addArgument('lastName', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addOption('super-admin', null, InputOption::VALUE_NONE, 'Grant ROLE_SUPER_ADMIN (can manage other admin users)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = strtolower(trim((string) $input->getArgument('email')));
        $password = (string) $input->getArgument('password');

        if (strlen($password) < 8) {
            $io->error('Password must be at least 8 characters long.');

            return Command::FAILURE;
        }

        if (null !== $this->adminUsers->findOneBy(['email' => $email])) {
            $io->error(sprintf('An admin user with email "%s" already exists.', $email));

            return Command::FAILURE;
        }

        $user = new AdminUser($email, (string) $input->getArgument('firstName'), (string) $input->getArgument('lastName'));
        $user->setRoles($input->getOption('super-admin') ? ['ROLE_SUPER_ADMIN'] : []);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('Admin user "%s" created.', $email));

        return Command::SUCCESS;
    }
}
