<?php

namespace Core\Service;

use Core\Mapper\ArticleMapper;
use Core\Mapper\ArticleDiscussionsMapper;
use Core\Entity\ArticleType;
use Core\Filter\ArticleFilter;
use Core\Filter\DiscussionFilter;
use Core\Exception\FilterException;
use Ramsey\Uuid\Uuid;
use MysqlUuid\Uuid as MysqlUuid;
use MysqlUuid\Formats\Binary;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Db\ResultSet\HydratingResultSet as ResultSet;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect;

class DiscussionService implements ArticleServiceInterface
{
    private $articleMapper;
    private $articleDiscussionsMapper;
    private $articleFilter;
    private $discussionFilter;

    public function __construct(ArticleMapper $articleMapper, ArticleDiscussionsMapper $articleDiscussionsMapper, ArticleFilter $articleFilter, DiscussionFilter $discussionFilter)
    {
        $this->articleMapper            = $articleMapper;
        $this->articleDiscussionsMapper = $articleDiscussionsMapper;
        $this->articleFilter            = $articleFilter;
        $this->discussionFilter         = $discussionFilter;
    }

    public function fetchAllArticles($page, $limit)
    {
        $select           = $this->articleDiscussionsMapper->getPaginationSelect();
        $paginatorAdapter = new DbSelect($select, $this->articleMapper->getAdapter());
        $paginator        = new Paginator($paginatorAdapter);

        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($limit);

        return $paginator;
    }

    public function fetchSingleArticle($articleId)
    {
        return $this->articleDiscussionsMapper->get($articleId);
    }

    public function saveArticle($user, $data, $id = null)
    {
        $articleFilter    = $this->articleFilter->getInputFilter()->setData($data);
        $discussionFilter = $this->discussionFilter->getInputFilter()->setData($data);

        if(!$articleFilter->isValid() || !$discussionFilter->isValid()){
            throw new FilterException($articleFilter->getMessages() + $discussionFilter->getMessages());
        }

        $article                    = $articleFilter->getValues();
        $discussion                 = $discussionFilter->getValues();
        $article['admin_user_uuid'] = $user->admin_user_uuid;

        if($id){
            $old = $this->articleDiscussionsMapper->get($id);
            $this->articleMapper->update($article, ['article_uuid' => $old->article_uuid]);
            $this->articleDiscussionsMapper->update($discussion, ['article_uuid' => $old->article_uuid]);
        }
        else{
            $article['type']            = ArticleType::DISCUSSION;
            $article['article_id']      = Uuid::uuid1()->toString();
            $article['article_uuid']    = (new MysqlUuid($article['article_id']))->toFormat(new Binary);
            $discussion['article_uuid'] = $article['article_uuid'];

            $this->articleMapper->insert($article);
            $this->articleDiscussionsMapper->insert($discussion);
        }
    }

    public function deleteArticle($id)
    {
        $discussion = $this->articleDiscussionsMapper->get($id);

        if(!$discussion){
            throw new \Exception('Article not found!');
        }

        $this->articleDiscussionsMapper->delete(['article_uuid' => $discussion->article_uuid]);
        $this->articleMapper->delete(['article_uuid' => $discussion->article_uuid]);
    }

}
