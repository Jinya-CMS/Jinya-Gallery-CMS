<?php

namespace Jinya\Framework\Security;

use Jinya\Services\Users\UserServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Throwable;

abstract class AuthenticatedCommand extends Command
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var UserServiceInterface */
    private $userService;

    /**
     * AuthenticatedCommand constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage, UserServiceInterface $userService)
    {
        parent::__construct();
        $this->tokenStorage = $tokenStorage;
        $this->userService = $userService;
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption('user', 'u', InputOption::VALUE_REQUIRED, 'The user to execute the action with');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $error = false;

        try {
            $user = $this->userService->getUserByEmail($input->getOption('user'));
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);
        } catch (Throwable $exception) {
            $error = true;
            $output->writeln("<error>\n\n\tYou need to specify a user running this command\n</error>");
        }

        if (!$error) {
            $this->authenticatedExecute($input, $output);
        }
    }

    abstract protected function authenticatedExecute(InputInterface $input, OutputInterface $output);
}