<?php
namespace PoP\Notifications\TypeResolvers;

use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;
use PoP\Notifications\TypeDataResolvers\NotificationTypeDataResolver;

class NotificationTypeResolver extends AbstractTypeResolver
{
	public const TYPE_COLLECTION_NAME = 'notifications';

    public function getTypeCollectionName(): string
    {
        return self::TYPE_COLLECTION_NAME;
    }

    public function getId($resultItem)
    {
        $notification = $resultItem;
        return $notification->histid;
    }

    public function getIdFieldTypeDataResolverClass(): string
    {
        return NotificationTypeDataResolver::class;
    }
}

