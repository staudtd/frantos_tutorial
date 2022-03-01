<?php

namespace App\Command\Install;

use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Basic command to import test customer
 */
abstract class AbstractCommand extends Command
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * Basic constructor with injection
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }
}
