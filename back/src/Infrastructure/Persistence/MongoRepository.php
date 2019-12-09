<?php


namespace App\Infrastructure\Persistence;


use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

abstract class MongoRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm, string $className)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata($className);
        parent::__construct($dm, $uow, $classMetaData);
    }
}