<?php


namespace App\MessegeHandler;


use App\Entity\News;
use App\Message\NewsParser;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NewsParserHandler implements MessageHandlerInterface
{
    public function __construct(LoggerInterface $logger,EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->logger = $logger;
        $this->logger->info('News person is now updating database');
    }
    public function __invoke(NewsParser $parser){
        try {
            $article = $parser->getLoad();
            $newsRepo = $this->manager->getRepository(News::class);

            if($existnews = $newsRepo->findOneBy(['title' => $article['title']])){
                $this->updateNewsItem($existnews, $article);
            }else{
                $this->createNewsItem($article);
            }
            $this->manager->flush();

            $this->logger->info("{$article['title']} is been processed.");
        }catch (\Throwable $th) {
            $this->logger->error($th->getMessage());
        }
    }

    /**
     * @param $article
     */
    protected function createNewsItem($article): void
    {
        $newItem = new News();
        $newItem->setTitle($article['title']);
        $newItem->setBody($article['content']);
        $newItem->setShortDescription($article['description']);
        $newItem->setAuthor($article['author']);
        $newItem->setSource($article['source']['name']);
        $newItem->setPicture($article['urlToImage']);
        $newItem->setUrl($article['url']);
        $newItem->setDateAdded(new \DateTime());
        $newItem->setUpdatedAt(new \DateTime());
        $this->manager->persist($newItem);
    }

    /**
     * @param $existnews
     * @param $article
     */
    protected function updateNewsItem(News $existnews, $article): void
    {
        $existnews->setBody($article['content']);
        $existnews->setShortDescription($article['description']);
        $existnews->setAuthor($article['author']);
        $existnews->setSource($article['source']['name']);
        $existnews->setPicture($article['urlToImage']);
        $existnews->setUpdatedAt(new \DateTime());
        $existnews->setUrl($article['url']);
    }

}