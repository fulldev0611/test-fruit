<?php

namespace App\Command;

use App\Entity\Fruit;
use App\Entity\Nutrition;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use GuzzleHttp\Client;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class FruitScraperCommand extends Command
{
    private $httpClient;
    private $entityManager;
    private $fruitRepository;
    private $mailer;

    protected static $defaultName = 'app:fruit-scraper';

    public function __construct(EntityManagerInterface $entityManager, FruitRepository $fruitRepository, MailerInterface $mailer)
    {
        parent::__construct();
        $this->httpClient = new \GuzzleHttp\Client();
        $this->entityManager = $entityManager;
        $this->fruitRepository = $fruitRepository;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Scrapes all fruits from https://fruityvice.com/ and saves them into the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting fruit scraper...');

        // Get all fruits from https://fruityvice.com/api/fruit/all
        $response = $this->httpClient->request('GET', 'https://fruityvice.com/api/fruit/all');
        $fruits = json_decode($response->getBody(), true);

        // Save fruits into database
        $entityManager = $this->entityManager;
        foreach ($fruits as $fruit) {
            // Check if fruit already exists in database
            $existingFruit = $entityManager->getRepository(Fruit::class)->findOneBy(['name' => $fruit['name']]);
            if (!$existingFruit) {
                // Create Nutrition entity and persist it to get the id
                $nutrition = new Nutrition();
                $nutrition->setCarbohydrates($fruit['nutritions']['carbohydrates']);
                $nutrition->setProtein($fruit['nutritions']['protein']);
                $nutrition->setFat($fruit['nutritions']['fat']);
                $nutrition->setCalories($fruit['nutritions']['calories']);
                $nutrition->setSugar($fruit['nutritions']['sugar']);
                $entityManager->persist($nutrition);
                $entityManager->flush();
        
                // Create Fruit entity and associate it with the Nutrition entity
                $newFruit = new Fruit();
                $newFruit->setName($fruit['name']);
                $newFruit->setGenus($fruit['genus']);
                $newFruit->setFamily($fruit['family']);
                $newFruit->setOrder($fruit['order']);
                $newFruit->setNutrition($nutrition);

                $entityManager->persist($newFruit);
                $entityManager->flush();

                $this->mailer->send((new Email())
                    ->from('vadimbabkovtc21@gmail.com')
                    ->to('vadimbabkovtc21+test1@gmail.com')
                    ->subject('New Fruit Added')
                    ->text('A new fruit has been added: ' . $fruit['name'])
                );
            }
        }
        

        $entityManager->flush();
        $output->writeln('Fruit scraper finished.');

        return Command::SUCCESS;
    }

}
