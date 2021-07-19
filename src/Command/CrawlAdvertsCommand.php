<?php

namespace App\Command;

use App\Crawler\CrawlerInterface;
use App\Entity\Advert;
use App\Model\Advert as AdvertModel;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;

class CrawlAdvertsCommand extends Command
{
    protected static $defaultName = 'app:crawl-adverts';

    private EntityManagerInterface $em;

    private AdvertRepository $advertRepository;

    private MailerInterface $mailer;

    /**
     * @var CrawlerInterface[]
     */
    private iterable $crawlers;
    private string $emailSender;
    private string $emailRecipient;

    /**
     * @param EntityManagerInterface $em
     * @param AdvertRepository $advertRepository
     * @param MailerInterface $mailer
     * @param iterable|CrawlerInterface[] $crawlers
     */
    public function __construct(
        EntityManagerInterface $em,
        AdvertRepository $advertRepository,
        MailerInterface $mailer,
        iterable $crawlers,
        string $emailSender,
        string $emailRecipient
    ) {
        parent::__construct();

        $this->em = $em;
        $this->advertRepository = $advertRepository;
        $this->mailer = $mailer;
        $this->crawlers = $crawlers;
        $this->emailSender = $emailSender;
        $this->emailRecipient = $emailRecipient;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Crawls apartment deals in the most popular websites');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Crawling is starting');

        foreach ($this->crawlers as $crawler) {
            $advertModels = $crawler->crawlAdverts();
            foreach ($advertModels as $advertModel) {
                $this->handleAdvert($advertModel);
            }

            $io->info(\get_class($crawler) . ' total: ' . \count($advertModels));
        }
        $this->em->flush();

        $io->success('Crawling successfully finished.');

        return 0;
    }
    /**
     * @param AdvertModel $advertModel
     */
    private function handleAdvert(AdvertModel $advertModel): void
    {
        $advertEntity = $this->advertRepository->findOneBy(['url' => $advertModel->getUrl()]);
        if ($advertEntity !== null) {
            return;
        }

        $advertEntity = new Advert(
            $advertModel->getUrl(),
            $advertModel->getAddress(),
            $advertModel->getRoomCount(),
            $advertModel->getArea(),
            $advertModel->getPrice(),
        );
        $this->em->persist($advertEntity);
        $this->sendEmail($advertModel);
    }


    protected function sendEmail(AdvertModel $advertModel): void
    {
        $this->mailer->send(
            (new TemplatedEmail())
                ->from($this->emailSender)
                ->to($this->emailRecipient)
                ->subject(
                    \sprintf(
                        '%s, %s, %s, %s',
                        $advertModel->getAddress(),
                        $advertModel->getRoomCount(),
                        $advertModel->getArea(),
                        $advertModel->getPrice(),
                    )
                )
                ->htmlTemplate('email.html.twig')
                ->context(
                    [
                        'url' => $advertModel->getUrl(),
                        'address' => $advertModel->getAddress(),
                        'room_count' => $advertModel->getRoomCount(),
                        'area' => $advertModel->getArea(),
                        'price' => $advertModel->getPrice(),
                    ]
                )
        );
    }
}
