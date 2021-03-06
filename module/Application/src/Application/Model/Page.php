<?php

namespace Application\Model;

use Application\Mapper\SiteMapperInterface;
use Application\Mapper\PageMapperInterface;
use Application\Mapper\AuthorshipMapperInterface;
use Application\Mapper\RevisionMapperInterface;
use Application\Mapper\VoteMapperInterface;
use Application\Mapper\TagMapperInterface;

class Page implements PageInterface
{
    /**
     * @var \Application\Mapper\SiteMapperInterface
     */
    protected $siteMapper;
    
    /**
     *
     * @var \Application\Mapper\PageMapperInterface
     */
    protected $pageMapper;    
    
    /**
     *
     * @var \Application\Mapper\AuthorMapperInterface
     */
    protected $authorMapper;

    /**
     *
     * @var \Application\Mapper\RevisionMapperInterface
     */
    protected $revisionMapper;
    
    /**
     *
     * @var \Application\Mapper\VoteMapperInterface
     */
    protected $voteMapper;    

    /**
     *
     * @var \Application\Mapper\TagMapperInterface
     */
    protected $tagMapper;    
    
    /**
     *
     * @var int
     */
    protected $siteId;
    
    /**
     *
     * @var \Application\Model\SiteInterface
     */
    protected $site;
    
    /**
     *
     * @var int
     */    
    protected $id;
    
    /**
     *
     * @var string
     */    
    protected $name;
    
    /**
     *
     * @var string
     */    
    protected $title;

    /**
     *
     * @var string
     */    
    protected $altTitle;
    
    /**
     *
     * @var int
     */    
    protected $categoryId;
    
    /**
     *
     * @var \DateTime
     */
    protected $creationDate;
    
    /**
     * @var string
     */
    protected $source;

    /**
     * @var bool
     */
    protected $hideSource;
    
    /**
     *
     * @var int
     */    
    protected $rating;
    
    /**
     *
     * @var int
     */    
    protected $cleanRating;
    
    /**
     *
     * @var int
     */    
    protected $contributorRating;
    
    /**
     *
     * @var int
     */    
    protected $adjustedRating;

    /**
     *
     * @var double
     */    
    protected $wilsonScore;
    
    /**
     *
     * @var int
     */        
    protected $revisionCount;

    /**
     * 
     * @var RevisionInterface[]
     */
    protected $revisions;

    /**
     *
     * @var int
     */        
    protected $voteCount;

    /**
     * 
     * @var VoteInterface[]
     */
    protected $votes;

    /**
     *
     * @var AuthorshipInterface[]
     */        
    protected $authors;
    
    /**
     * @var int
     */
    protected $status;
    
    /**
     *
     * @var int
     */
    protected $originalId;

    /**
     *
     * @var PageInterface
     */
    protected $original;
    
    /**
     * @var PageInterface[]
     */
    protected $translations;

    /**
     * @var int
     */
    protected $kind;    
    
    /**
     *
     * @var int
     */
    protected $rank;
    
    /**
     * @var array[string]
     */
    protected $tags;
    
    /**
     * @var bool
     */
    protected $deleted;
    
    /**
     * @var \DateTime
     */
    protected $lastUpdate;
    
    /**
     * Constructor
     * @param SiteMapperInterface $siteMapper
     * @param PageMapperInterface $pageMapper
     * @param AuthorshipMapperInterface $authorMapper
     * @param RevisionMapperInterface $revisionMapper
     * @param VoteMapperInterface $voteMapper
     * @param TagMapperInterface $tagMapper
     */
    public function __construct(
            SiteMapperInterface $siteMapper,
            PageMapperInterface $pageMapper,
            AuthorshipMapperInterface $authorMapper, 
            RevisionMapperInterface $revisionMapper,
            VoteMapperInterface $voteMapper,
            TagMapperInterface $tagMapper
    ) 
    { 
        $this->siteMapper = $siteMapper;
        $this->pageMapper = $pageMapper;
        $this->authorMapper = $authorMapper;
        $this->revisionMapper = $revisionMapper;
        $this->voteMapper = $voteMapper;
        $this->tagMapper = $tagMapper;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    public function setSiteId($value)
    {
        $this->siteId = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getSite() 
    {
        if (!isset($this->site)) {
            $this->site = $this->siteMapper->find($this->getSiteId());
        }
        return $this->site;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($value)
    {
        $this->name = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($value)
    {
        $this->title = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getAltTitle()
    {
        return $this->altTitle;
    }
    
    public function setAltTitle($value)
    {
        $this->altTitle = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }    

    public function setCategoryId()
    {
        return $this->categoryId;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    public function setSource($value)
    {
        $this->source = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getHideSource()
    {
        return $this->hideSource;
    }

    public function setHideSource($value)
    {
        $this->hideSource = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
    
    public function setCreationDate(\DateTime $value)
    {
        $this->creationDate = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getCleanRating() 
    {
        return $this->cleanRating;
    }

    public function setCleanRating($value)
    {
        $this->cleanRating = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRating() 
    {
        return $this->rating;
    }

    public function setRating($value)
    {
        $this->rating = $value;
    }    

    /**
     * {@inheritDoc}
     */    
    public function getContributorRating() 
    {
        return $this->contributorRating;
    }

    public function setContributorRating($contributorRating) 
    {
        $this->contributorRating = $contributorRating;
    }

    /**
     * {@inheritDoc}
     */
    public function getAdjustedRating() 
    {
        return $this->adjustedRating;
    }

    public function setAdjustedRating($adjustedRating) 
    {
        $this->adjustedRating = $adjustedRating;
    }

    /**
     * {@inheritDoc}
     */
    public function getWilsonScore()
    {
        return $this->wilsonScore;
    }

    public function setWilsonScore($wilsonScore)
    {
        $this->wilsonScore = $wilsonScore;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRevisionCount() 
    {
        if (isset($this->revisions)) {
            return count($this->revisions);
        } else {
            return $this->revisionCount;
        }
    }
    
    public function setRevisionCount($value)
    {
        $this->revisionCount = $value;
    }

    public function getRevisions() 
    {
        /*if (!isset($this->revisions)) {
            $this->revisions = $this->revisionMapper->findRevisionsOfPage($this->getId());
        }*/
        return $this->revisions;
    }    
    
    /**
     * {@inheritDoc}
     */
    public function getAuthors() 
    {
        if (!isset($this->authors)) {
            $this->authors = [];
            $authors = $this->authorMapper->findAuthorshipsOfPage($this->getId());
            foreach ($authors as $author) {
                $this->authors[] = $author;
            }
        }
        return $this->authors;
    }

    /**
     * 
     * @param \Application\Model\Authorship[] $authors
     */
    public function setAuthors($authors)
    {
        $this->authors = $authors;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getVoteCount() 
    {
        if (isset($this->votes)) {
            return count($this->votes);
        } else {
            return $this->voteCount;
        }        
    }

    /**
     * {@inheritDoc}
     */
    public function getVotes() 
    {
        if (!isset($this->votes)) {
            $this->votes = $this->voteMapper->findVotesOnPage($this->getId());
        }
        return $this->votes;        
    }

    /**
     * {@inheritDoc}
     */
    public function getOriginal() 
    {   
        if ($this->originalId === $this->getId()) {
            return $this;
        }
        if (!isset($this->original)) {
            if (!$this->originalId) {
                return null;
            }
            $this->original = $this->pageMapper->find($this->originalId);
        }
        return $this->original;
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslations()
    {
       if (!isset($this->translations)) {
           $this->translations = $this->pageMapper->findTranslations($this->getId());
       }
       return $this->translations;
    }
    
    public function getOriginalId()
    {
        return $this->originalId;
    }
    
    public function setOriginalId($value)
    {
        $this->originalId = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getStatus() 
    {
        return $this->status;
    }
    
    public function setStatus($value)
    {
        $this->status = $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getKind()
    {
        return $this->kind;
    }

    public function setKind($kind)
    {
        $this->kind = $kind;
    }
        
    /**
     * {@inheritDoc}
     */
    public function getRank()
    {
        if (!isset($this->rank)) {
            $this->rank = $this->pageMapper->findPageRank($this->getId());
        }
        return $this->rank;
    }

    public function setRank($rank)
    {
        $this->rank = $rank;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        if (!isset($this->tags)) {
            $this->tags = [];
            $tags = $this->tagMapper->findPageTags($this->getId());
            foreach ($tags as $tag) {
                $this->tags[] = $tag->getTag();
            }
        }
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return sprintf('/page/%d', $this->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
 
    public function setDeleted($value)
    {
        $this->deleted = (bool) $value;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }
    
    public function setLastUpdate($value)
    {
        $this->lastUpdate = $value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $authors = [];
        foreach ($this->getAuthors() as $author) {
            $authors[] = [
                'id' => $author->getUser()->getId(),
                'user' => $author->getUser()->getDisplayName(),                
                'role' => \Application\Utils\AuthorRole::getDescription($author->getRole())
            ];
        }        
        $result = [
            'id' => $this->getId(),
            'site' => $this->getSite()->getUrl(),            
            'name' => $this->getName(),            
            'title' => $this->getTitle(),
            'altTitle' => $this->getAltTitle(),
            'status' => \Application\Utils\PageStatus::getDescription($this->getStatus()),
            'kind' => \Application\Utils\PageKind::getDescription($this->getKind()),
            'creationDate' => $this->getCreationDate(),
            'rating' => $this->getRating(),
            'cleanRating' => $this->getCleanRating(),
            'contributorRating' => $this->getContributorRating(),
            'adjustedRating' => $this->getAdjustedRating(),
            'wilsonScore' => $this->getWilsonScore(),
            'rank' => $this->getRank(),
            'authors' => $authors,
            'deleted' => $this->getDeleted()
            ];        
        return $result;
    }
}