<?php

namespace App\Command;

use App\Entity\News;
use App\Message\NewsParser;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:parse-news',
    description: 'News parser service',
)]
class ParseNewsCommand extends Command
{

    public function __construct(HttpClientInterface $client, MessageBusInterface $bus, $projectDir)
    {
        $this->client = $client;
        $this->bus = $bus;
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription("This will parse the new to the database")
            ->addArgument('url', InputArgument::OPTIONAL, "News Feed URL",
                "https://newsapi.org/v2/everything?q=Apple&from=2022-11-27&sortBy=popularity&apiKey=35cb4027ec204b7896c94469be74c445");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $url = $input->getArgument('url');

            $response = $this->client->request('GET', $url);
            $fs = new \Symfony\Component\Filesystem\Filesystem();

            $fs->dumpFile($this->projectDir ."/src/Command/sample.json", $response->getContent());

            $contents = $response->toArray();
            foreach ($contents['articles'] as $article){
                $this->bus->dispatch(new NewsParser($article));
            }
            $io = new SymfonyStyle($input, $output);

            $io->success("News Parser service is been initiated.");

            return Command::SUCCESS;
        } catch(IOException $e) {
            $this->logger->error($e->getMessage());
        }
    }

}
