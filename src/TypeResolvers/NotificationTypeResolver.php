<?php
namespace PoP\Notifications\TypeResolvers;

use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;
use PoP\Notifications\TypeDataLoaders\NotificationTypeDataLoader;

class NotificationTypeResolver extends AbstractTypeResolver
{
	public const NAME = 'Notification';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getId($resultItem)
    {
        $notification = $resultItem;
        return $notification->histid;
    }

    public function getTypeDataLoaderClass(): string
    {
        return NotificationTypeDataLoader::class;
    }
}

