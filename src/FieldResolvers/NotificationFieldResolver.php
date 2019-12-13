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
            'object-type',
            'object-subtype',
            'object-name',
            'object-id',
            'user-id',
            'website-url',
            'user-caps',
            'hist-ip',
            'hist-time',
            'hist-time-nogmt',
            'hist-time-readable',
            'status',
            'is-status-read',
            'is-status-not-read',
            'mark-as-read-url',
            'mark-as-unread-url',
            'icon',
            'url',
            'target',
            'message',
            'is-post-notification',
            'is-user-notification',
            'is-comment-notification',
            'is-taxonomy-notification',
            'is-action',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'action' => SchemaDefinition::TYPE_STRING,
            'object-type' => SchemaDefinition::TYPE_STRING,
            'object-subtype' => SchemaDefinition::TYPE_STRING,
            'object-name' => SchemaDefinition::TYPE_STRING,
            'object-id' => SchemaDefinition::TYPE_ID,
            'user-id' => SchemaDefinition::TYPE_ID,
            'website-url' => SchemaDefinition::TYPE_URL,
            'user-caps' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_STRING),
            'hist-ip' => SchemaDefinition::TYPE_IP,
            'hist-time' => SchemaDefinition::TYPE_DATE,
            'hist-time-nogmt' => SchemaDefinition::TYPE_DATE,
            'hist-time-readable' => SchemaDefinition::TYPE_STRING,
            'status' => SchemaDefinition::TYPE_STRING,
            'is-status-read' => SchemaDefinition::TYPE_BOOL,
            'is-status-not-read' => SchemaDefinition::TYPE_BOOL,
            'mark-as-read-url' => SchemaDefinition::TYPE_URL,
            'mark-as-unread-url' => SchemaDefinition::TYPE_URL,
            'icon' => SchemaDefinition::TYPE_STRING,
            'url' => SchemaDefinition::TYPE_URL,
            'target' => SchemaDefinition::TYPE_STRING,
            'message' => SchemaDefinition::TYPE_STRING,
            'is-post-notification' => SchemaDefinition::TYPE_BOOL,
            'is-user-notification' => SchemaDefinition::TYPE_BOOL,
            'is-comment-notification' => SchemaDefinition::TYPE_BOOL,
            'is-taxonomy-notification' => SchemaDefinition::TYPE_BOOL,
            'is-action' => SchemaDefinition::TYPE_BOOL,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
			'action' => $translationAPI->__('', ''),
            'object-type' => $translationAPI->__('', ''),
            'object-subtype' => $translationAPI->__('', ''),
            'object-name' => $translationAPI->__('', ''),
            'object-id' => $translationAPI->__('', ''),
            'user-id' => $translationAPI->__('', ''),
            'website-url' => $translationAPI->__('', ''),
            'user-caps' => $translationAPI->__('', ''),
            'hist-ip' => $translationAPI->__('', ''),
            'hist-time' => $translationAPI->__('', ''),
            'hist-time-nogmt' => $translationAPI->__('', ''),
            'hist-time-readable' => $translationAPI->__('', ''),
            'status' => $translationAPI->__('', ''),
            'is-status-read' => $translationAPI->__('', ''),
            'is-status-not-read' => $translationAPI->__('', ''),
            'mark-as-read-url' => $translationAPI->__('', ''),
            'mark-as-unread-url' => $translationAPI->__('', ''),
            'icon' => $translationAPI->__('', ''),
            'url' => $translationAPI->__('', ''),
            'target' => $translationAPI->__('', ''),
            'message' => $translationAPI->__('', ''),
            'is-post-notification' => $translationAPI->__('', ''),
            'is-user-notification' => $translationAPI->__('', ''),
            'is-comment-notification' => $translationAPI->__('', ''),
            'is-taxonomy-notification' => $translationAPI->__('', ''),
            'is-action' => $translationAPI->__('', ''),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function getSchemaFieldArgs(TypeResolverInterface $typeResolver, string $fieldName): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'is-action':
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

    public function resolveSchemaValidationErrorDescription(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'is-action':
                $action = $fieldArgs['action'];
                if (!$action) {
                    return $translationAPI->__('Argument \'action\' cannot be empty', 'pop-posts');
                }
                return null;
        }

        return parent::resolveSchemaValidationErrorDescription($typeResolver, $fieldName, $fieldArgs);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $notification = $resultItem;
        $cmsengineapi = \PoP\Engine\FunctionAPIFactory::getInstance();
        $cmscommentsapi = \PoP\Comments\FunctionAPIFactory::getInstance();
        $cmsusersapi = \PoP\Users\FunctionAPIFactory::getInstance();
        $cmspostsapi = \PoP\Posts\FunctionAPIFactory::getInstance();
        $taxonomyapi = \PoP\Taxonomies\FunctionAPIFactory::getInstance();
        $cmscommentsresolver = \PoP\Comments\ObjectPropertyResolverFactory::getInstance();
        switch ($fieldName) {
            case 'action':
                return $notification->action;
            case 'object-type':
                return $notification->object_type;
            case 'object-subtype':
                return $notification->object_subtype;
            case 'object-name':
                return $notification->object_name;
            case 'object-id':
                return $notification->object_id;
            case 'user-id':
                return $notification->user_id;
            case 'website-url':
                return $cmsusersapi->getUserURL($notification->user_id);
            case 'user-caps':
                return $notification->user_caps;
            case 'hist-ip':
                return $notification->hist_ip;
            case 'hist-time':
                return $notification->hist_time;
            case 'hist-time-nogmt':
                // In the DB, the time is saved without GMT. However, in the front-end we need the GMT factored in,
                // because moment.js will
                return $notification->hist_time - ($cmsengineapi->getOption(NameResolverFacade::getInstance()->getName('popcms:option:gmtOffset')) * 3600);
            case 'hist-time-readable':
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

            case 'is-status-read':
                $status = $typeResolver->resolveValue($resultItem, 'status', $variables, $expressions, $options);
                return ($status == AAL_POP_STATUS_READ);

            case 'is-status-not-read':
                $is_read = $typeResolver->resolveValue($resultItem, 'is-status-read', $variables, $expressions, $options);
                return !$is_read;

            case 'mark-as-read-url':
                return GeneralUtils::addQueryArgs([
                    'nid' => $typeResolver->getId($notification),
                ], RouteUtils::getRouteURL(POP_NOTIFICATIONS_ROUTE_NOTIFICATIONS_MARKASREAD));

            case 'mark-as-unread-url':
                return GeneralUtils::addQueryArgs([
                    'nid' => $typeResolver->getId($notification),
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
                        return $cmspostsapi->getPermalink($notification->object_id);

                    case 'User':
                        return $cmsusersapi->getUserURL($notification->object_id);

                    case 'Taxonomy':
                        return $taxonomyapi->getTermLink($notification->object_id);

                    case 'Comments':
                        $comment = $cmscommentsapi->getComment($notification->object_id);
                        return $cmspostsapi->getPermalink($cmscommentsresolver->getCommentPostId($comment));
                }
                return null;

            case 'target':
                // By default, no need to specify the target. This can be overriden
                return null;

            case 'message':
                return $notification->object_name;

            case 'is-post-notification':
                return $notification->object_type == 'Post';

            case 'is-user-notification':
                return $notification->object_type == 'User';

            case 'is-comment-notification':
                return $notification->object_type == 'Comments';

            case 'is-taxonomy-notification':
                return $notification->object_type == 'Taxonomy';

            case 'is-action':
                return $fieldArgs['action'] == $notification->action;
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }

    public function resolveFieldTypeResolverClass(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        switch ($fieldName) {
            case 'user-id':
                return UserTypeResolver::class;
        }

        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
