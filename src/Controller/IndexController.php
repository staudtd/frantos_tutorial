<?php
namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * IndexController
 */
class IndexController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository
     */
    private $customerRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->customerRepository = $em->getRepository('App:Customer');
    }

    /**
     * Startseite
     *
     * @Route("/")
     */
    public function index(): Response
    {
        $customers = $this->customerRepository->findAll();

        return $this->render('index.html.twig', ['customers' => $customers]);
    }

    /**
     * Download order pdf
     *
     * @Route("/download/{order}")
     */
    public function download(Order $order): Response
    {
        $dompdf = new Dompdf([]);

        $html = $this->renderView('download/pdf.html.twig', [
            'customer' => $order->getCustomer(),
            'order' => $order
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("testpdf.pdf", [
            "Attachment" => true
        ]);
    }
}