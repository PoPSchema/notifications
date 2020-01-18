<?php
namespace PoP\Notifications\FieldResolvers;

use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\GeneralUtils;
use PoP\LooseContracts\Facades\NameResolverFacade;
use PoP\Engine\Route\RouteUtils;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Users\TypeResolvers\UserTypeResolver;
use PoP\Notifications\TypeResolvers\NotificationTypeResolver;
use PoP\Posts\Facades\PostTypeAPIFacade;

class NotificationFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(NotificationTypeResolver::class);
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'action',
            'objectType',
            'objectSubtype',
            'objectName',
            'objectID',
            'userID',
            'websiteURL',
            'userCaps',
            'histIp',
            'histTime',
            'histTimeNogmt',
            'histTimeReadable',
            'status',
            'isStatusRead',
            'isStatusNotRead',
            'markAsReadURL',
            'markAsUnreadURL',
            'icon',
            'url',
            'target',
            'message',
            'isPostNotification',
            'isUserNotification',
            'isCommentNotification',
            'isTaxonomyNotification',
            'isAction',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'action' => SchemaDefinition::TYPE_STRING,
            'objectType' => SchemaDefinition::TYPE_STRING,
            'objectSubtype' => SchemaDefinition::TYPE_STRING,
            'objectName' => SchemaDefinition::TYPE_STRING,
            'objectID' => SchemaDefinition::TYPE_ID,
            'userID' => SchemaDefinition::TYPE_ID,
            'websiteURL' => SchemaDefinition::TYPE_URL,
            'userCaps' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_STRING),
            'histIp' => SchemaDefinition::TYPE_IP,
            'histTime' => SchemaDefinition::TYPE_DATE,
            'histTimeNogmt' => SchemaDefinition::TYPE_DATE,
            'histTimeReadable' => SchemaDefinition::TYPE_STRING,
            'status' => SchemaDefinition::TYPE_STRING,
            'isStatusRead' => SchemaDefinition::TYPE_BOOL,
            'isStatusNotRead' => SchemaDefinition::TYPE_BOOL,
            'markAsReadURL' => SchemaDefinition::TYPE_URL,
            'markAsUnreadURL' => SchemaDefinition::TYPE_URL,
            'icon' => SchemaDefinition::TYPE_STRING,
            'url' => SchemaDefinition::TYPE_URL,
            'target' => SchemaDefinition::TYPE_STRING,
            'message' => SchemaDefinition::TYPE_STRING,
            'isPostNotification' => SchemaDefinition::TYPE_BOOL,
            'isUserNotification' => SchemaDefinition::TYPE_BOOL,
            'isCommentNotification' => SchemaDefinition::TYPE_BOOL,
            'isTaxonomyNotification' => SchemaDefinition::TYPE_BOOL,
            'isAction' => SchemaDefinition::TYPE_BOOL,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
			'action' => $translationAPI->__('', ''),
            'objectType' => $translationAPI->__('', ''),
            'objectSubtype' => $translationAPI->__('', ''),
            'objectName' => $translationAPI->__('', ''),
            'objectID' => $translationAPI->__('', ''),
            'userID' => $translationAPI->__('', ''),
            'websiteURL' => $translationAPI->__('', ''),
            'userCaps' => $translationAPI->__('', ''),
            'histIp' => $translationAPI->__('', ''),
            'histTime' => $translationAPI->__('', ''),
            'histTimeNogmt' => $translationAPI->__('', ''),
            'histTimeReadable' => $translationAPI->__('', ''),
            'status' => $translationAPI->__('', ''),
            'isStatusRead' => $translationAPI->__('', ''),
            'isStatusNotRead' => $translationAPI->__('', ''),
            'markAsReadURL' => $translationAPI->__('', ''),
            'markAsUnreadURL' => $translationAPI->__('', ''),
            'icon' => $translationAPI->__('', ''),
            'url' => $translationAPI->__('', ''),
            'target' => $translationAPI->__('', ''),
            'message' => $translationAPI->__('', ''),
            'isPostNotification' => $translationAPI->__('', ''),
            'isUserNotification' => $translationAPI->__('', ''),
            'isCommentNotification' => $translationAPI->__('', ''),
            'isTaxonomyNotification' => $translationAPI->__('', ''),
            'isAction' => $translationAPI->__('', ''),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function getSchemaFieldArgs(TypeResolverInterface $typeResolver, string $fieldName): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'isAction':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'action',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The action to check against the notification', 'pop-posts'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];
        }

        return parent::getSchemaFieldArgs($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $notification = $resultItem;
        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
        $cmscommentsapi = \PoP\Comments\FunctionAPIFactory::getInstance();
        $cmsusersapi = \PoP\Users\FunctionAPIFactory::getInstance();
        $postTypeAPI = PostTypeAPIFacade::getInstance();
        $taxonomyapi = \PoP\Taxonomies\FunctionAPIFactory::getInstance();
        $cmscommentsresolver = \PoP\Comments\ObjectPropertyResolverFactory::getInstance();
        switch ($fieldName) {
            case 'action':
                return $notification->action;
            case 'objectType':
                return $notification->object_type;
            case 'objectSubtype':
                return $notification->object_subtype;
            case 'objectName':
                return $notification->object_name;
            case 'objectID':
                return $notification->object_id;
            case 'userID':
                return $notification->user_id;
            case 'websiteURL':
                return $cmsusersapi->getUserURL($notification->user_id);
            case 'userCaps':
                return $notification->user_caps;
            case 'histIp':
                return $notification->hist_ip;
            case 'histTime':
                return $notification->hist_time;
            case 'histTimeNogmt':
                // In the DB, the time is saved without GMT. However, in the front-end we need the GMT factored in,
                // because moment.js will
                return $notification->hist_time - ($cmsengineapi->getOption(NameResolverFacade::getInstance()->getName('popcms:option:gmtOffset')) * 3600);
            case 'histTimeReadable':
                // Must convert date using GMT
                return sprintf(
                    TranslationAPIFacade::getInstance()->__('%s ago', 'pop-notifications'),
                    \humanTiming($notification->hist_time - ($cmsengineapi->getOption(NameResolverFacade::getInstance()->getName('popcms:option:gmtOffset')) * 3600))
                );

            case 'status':
                $value = $notification->status;
                if (!$value) {
                    // Make sure to return an empty string back, since this is used as a class
                    return '';
                }
                return $value;

            case 'isStatusRead':
                $status = $typeResolver->resolveValue($resultItem, 'status', $variables, $expressions, $options);
                return ($status == AAL_POP_STATUS_READ);

            case 'isStatusNotRead':
                $is_read = $typeResolver->resolveValue($resultItem, 'isStatusRead', $variables, $expressions, $options);
                return !$is_read;

            case 'markAsReadURL':
                return GeneralUtils::addQueryArgs([
                    'nid' => $typeResolver->getID($notification),
                ], RouteUtils::getRouteURL(POP_NOTIFICATIONS_ROUTE_NOTIFICATIONS_MARKASREAD));

            case 'markAsUnreadURL':
                return GeneralUtils::addQueryArgs([
                    'nid' => $typeResolver->getID($notification),
                ], RouteUtils::getRouteURL(POP_NOTIFICATIONS_ROUTE_NOTIFICATIONS_MARKASUNREAD));

            case 'icon':
                // URL depends basically on the action performed on the object type
                switch ($notification->object_type) {
                    case 'Post':
                        return \gdGetPosticon($notification->object_id);
                }
                return null;

            case 'url':
                // URL depends basically on the action performed on the object type
                switch ($notification->object_type) {
                    case 'Post':
                        return $postTypeAPI->getPermalink($notification->object_id);

                    case 'User':
                        return $cmsusersapi->getUserURL($notification->object_id);

                    case 'Taxonomy':
                        return $taxonomyapi->getTermLink($notification->object_id);

                    case 'Comments':
                        $comment = $cmscommentsapi->getComment($notification->object_id);
                        return $postTypeAPI->getPermalink($cmscommentsresolver->getCommentPostId($comment));
                }
                return null;

            case 'target':
                // By default, no need to specify the target. This can be overriden
                return null;

            case 'message':
                return $notification->object_name;

            case 'isPostNotification':
                return $notification->object_type == 'Post';

            case 'isUserNotification':
                return $notification->object_type == 'User';

            case 'isCommentNotification':
                return $notification->object_type == 'Comments';

            case 'isTaxonomyNotification':
                return $notification->object_type == 'Taxonomy';

            case 'isAction':
                return $fieldArgs['action'] == $notification->action;
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }

    public function resolveFieldTypeResolverClass(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        switch ($fieldName) {
            case 'userID':
                return UserTypeResolver::class;
        }

        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
