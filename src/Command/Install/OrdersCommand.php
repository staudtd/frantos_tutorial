<?php

namespace App\Command\Install;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;

/**
 * Basic command to import test customer
 */
class OrdersCommand extends AbstractCommand
{
    const FIELDNAME_CUSTOMERNUMBER  = 'customernumber';
    const FIELDNAME_ORDER_ID        = 'order_id';
    const FIELDNAME_ARTICLENUMBER   = 'articlenumber';
    const FIELDNAME_ARTICLENAME     = 'articlename';
    const FIELDNAME_AMOUNT          = 'amount';
    const FIELDNAME_PRICE           = 'price';

    protected static $defaultName = 'install:orders';
    protected static $defaultDescription = 'Get order data for testing purposes';

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    protected $mapping = [
        self::FIELDNAME_CUSTOMERNUMBER,
        self::FIELDNAME_ORDER_ID,
        self::FIELDNAME_ARTICLENUMBER,
        self::FIELDNAME_ARTICLENAME,
        self::FIELDNAME_AMOUNT,
        self::FIELDNAME_PRICE
    ];

    protected $orders = [];

    protected function configure(): void
    {
        $this
            ->addArgument('filepath', InputArgument::REQUIRED, 'Path to import file')
            ->addArgument('filename', InputArgument::REQUIRED, 'Filename of import file')
        ;
    }

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
        $filepath = $input->getArgument('filepath');
        $filename = $input->getArgument('filename');

        $customerRepository = $this->em->getRepository('App:Customer');

        // Get Testcustomer
        /** @var Customer $customer */
        $customer = $customerRepository->findOneBy(['customer_id' => 1338]);

        // Check if install:customer was run first. Testcustomer is static for now
        if ($customer === null || !($customer instanceof Customer)) {
            $io->error('Testcustomer not initiated yet! Please run install:customer first.');
            return Command::INVALID;
        }

        try {
            // Load files to finder
            $finder = new Finder();
            $finder->files()
                ->in($filepath)
                ->name($filename);
        } catch (DirectoryNotFoundException $e) {
            $io->error(sprintf('Directory "%s" not found.', $filepath));
            return Command::INVALID;
        }

        if ($finder->count() === 0) {
            $io->error('No files found in given directory.');
            return Command::FAILURE;
        }

        $io->note('Iterating files.');

        // Iterate files
        foreach ($finder as $csv) {
            $io->note('---------');
            $io->note(sprintf('Opening file: "%s"', $csv->getRealPath()));

            try {
                $handle = fopen($csv->getRealPath(), "r");

                if ($handle === false) {
                    throw new \Exception(sprintf('Failed to open file.', $csv->getRealPath()));
                }

                // Load 1. line of csv as header
                $header = fgetcsv($handle, null, ',');

                if (empty($header)) {
                    throw new \Exception('First line/header of CSV is empty.');
                }

                $header = array_flip($header);

                // Validate header for required fields
                foreach ($this->mapping as $field) {
                    if (!array_key_exists($field, $header)) {
                        throw new \Exception('Header is formatted wrong.');
                    }
                }

                $i = 1;
                while ($data = fgetcsv($handle, null, ',')) {
                    $i++;
                    $orderID = $data[$header[self::FIELDNAME_ORDER_ID]] ?? false;

                    if (!$orderID) {
                        $io->error(sprintf('Order ID not set in row %s', $i));
                        continue;
                    }

                    $order = $this->getOrder($orderID, $customer);

                    $orderItem = new OrderItem();
                    $orderItem->setParent($order);
                    $orderItem->setArticlenumber($data[$header[self::FIELDNAME_ARTICLENUMBER]]);
                    $orderItem->setArticlename($data[$header[self::FIELDNAME_ARTICLENAME]]);
                    $orderItem->setAmount($data[$header[self::FIELDNAME_AMOUNT]]);
                    $orderItem->setPrice($data[$header[self::FIELDNAME_PRICE]]);

                    $io->note(sprintf('Created orderitem for row %s', $i));

                    $this->em->persist($orderItem);
                }
            } catch (\Exception $e) {
                // TODO: implement custom exceptions
                $io->error($e->getMessage());
            }
        }

        $this->em->flush();

        return Command::SUCCESS;
    }

    public function getOrder(int $orderId, Customer $customer): Order
    {
        if ($this->orderRepository === null) {
            $this->orderRepository = $this->em->getRepository('App:Order');
        }

        if (isset($this->orders[$orderId])) {
            return $this->orders[$orderId];
        }

        $order = $this->orderRepository->findOneBy(['order_id' => $orderId]);

        if ($order === null) {
            $order = new Order();
            $order->setCustomer($customer);
            $order->setOrderId($orderId);

            $this->em->persist($order);
        }

        $this->orders[$orderId] = $order;

        return $this->orders[$orderId];
    }
}
