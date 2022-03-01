<?php

namespace App\Command\Install;

use App\Entity\Customer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Basic command to import test customer
 */
class CustomerCommand extends AbstractCommand
{
    protected static $defaultName = 'install:customer';
    protected static $defaultDescription = 'Get customer data for testing purposes';

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $customerRepository = $this->em->getRepository('App:Customer');

        /** @var Customer $customer */
        $customer = $customerRepository->findOneBy(['customer_id' => 1338]);

        if ($customer !== null) {
            $io->error('Testcustomer already exists!');
            return Command::INVALID;
        }

        // Initialize customer object
        $customer = new Customer();
        $customer->setCustomerId(1338);
        $customer->setFirstname('Tebin');
        $customer->setLastname('Ulrich');
        $customer->setStreet('Teststrasse 1');
        $customer->setPostcode('12345');
        $customer->setCity('Berlin');
        $customer->setEmail('test@test.de');

        // Persist customer object
        $this->em->persist($customer);
        $this->em->flush();

        $io->success('Customer created!');

        return Command::SUCCESS;
    }
}
