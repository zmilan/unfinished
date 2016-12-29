<?php

namespace Admin\Factory\Model\Repository;

use Core\Mapper\ArticleMapper;
use Admin\Model\Repository\ArticleRepository;
use Interop\Container\ContainerInterface;

class ArticleRepositoryFactory
{
    /**
     * @param ContainerInterface $container
     * @return ArticleRepository
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ArticleRepository(
            $container->get(ArticleMapper::class),
            new \DateTime()
        );
    }
}
