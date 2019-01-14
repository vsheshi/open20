<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */
$modules['admin'] = [
    'class' => 'lispa\amos\admin\AmosAdmin',
    'htmlMailContent' => '@backend/mail/user/credenziali-html',
    'htmlSendCredentialMailContent' => '@backend/mail/user/credenziali-utente-loggato-html',
    'htmlSendCredentialMailSubject' => '@backend/mail/user/credenziali-utente-loggato-subject',
    'enableUserContacts' => false,
    'bypassWorkflow' => true,
    'hideWidgetGraphicsActions' => true,
    'profileRequiredFields' => [
        'nome',
        'cognome',
        'status',
    ],
    'fieldsConfigurations' => [
        'boxes' => [
            'box_account_data' => ['form' => true, 'view' => true],
            'box_dati_accesso' => ['form' => true, 'view' => true],
            'box_dati_contatto' => ['form' => true, 'view' => true],
            'box_dati_fiscali_amministrativi' => ['form' => false, 'view' => false],
            'box_dati_nascita' => ['form' => false, 'view' => false],
            'box_email_frequency' => ['form' => false, 'view' => false],
            'box_facilitatori' => ['form' => false, 'view' => false],
            'box_foto' => ['form' => true, 'view' => true],
            'box_informazioni_base' => ['form' => true, 'view' => true],
            'box_presentazione_personale' => ['form' => true, 'view' => true],
            'box_prevalent_partnership' => ['form' => false, 'view' => false],
            'box_privacy' => ['form' => true, 'view' => true],
            'box_questio' => ['form' => false, 'view' => false],
            'box_role_and_area' => ['form' => true, 'view' => true],
            'box_social_account' => ['form' => false, 'view' => false],
        ],
        'fields' => [
            'attivo' => ['form' => true, 'view' => true, 'referToBox' => 'box_account_data'],
            'codice_fiscale' => ['form' => false, 'view' => false, 'referToBox' => 'box_dati_fiscali_amministrativi'],
            'cognome' => ['form' => true, 'view' => true, 'referToBox' => 'box_informazioni_base'],
            'default_facilitatore' => ['form' => true, 'view' => true],
            'email' => ['form' => true, 'view' => true, 'referToBox' => 'box_dati_contatto'],
            'email_pec' => ['form' => false, 'view' => false, 'referToBox' => 'box_dati_contatto'],
            'facebook' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
            'facilitatore_id' => ['form' => true, 'view' => true, 'referToBox' => 'box_facilitatori'],
            'googleplus' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
            'linkedin' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
            'nascita_comuni_id' => ['form' => false, 'view' => false, 'referToBox' => 'box_dati_nascita'],
            'nascita_data' => ['form' => false, 'view' => false, 'referToBox' => 'box_dati_nascita'],
            'nascita_nazioni_id' => ['form' => false, 'view' => false, 'referToBox' => 'box_dati_nascita'],
            'nascita_province_id' => ['form' => false, 'view' => false, 'referToBox' => 'box_dati_nascita'],
            'nome' => ['form' => true, 'view' => true, 'referToBox' => 'box_informazioni_base'],
            'note' => ['form' => false, 'view' => false, 'referToBox' => 'box_informazioni_base'],
            'presentazione_breve' => ['form' => true, 'view' => true, 'referToBox' => 'box_informazioni_base'],
            'presentazione_personale' => [
                'form' => true,
                'view' => true,
                'referToBox' => 'box_presentazione_personale'
            ],
            'prevalent_partnership_id' => ['form' => true, 'view' => true, 'referToBox' => 'box_prevalent_partnership'],
            'privacy' => ['form' => true, 'view' => true, 'referToBox' => 'box_privacy'],
            'sesso' => ['form' => true, 'view' => false, 'referToBox' => 'box_informazioni_base'],
            'telefono' => ['form' => true, 'view' => false, 'referToBox' => 'box_dati_contatto'],
            'twitter' => ['form' => true, 'view' => true, 'referToBox' => 'box_social_account'],
            'ultimo_accesso' => ['form' => true, 'view' => true, 'referToBox' => 'box_account_data'],
            'ultimo_logout' => ['form' => true, 'view' => true, 'referToBox' => 'box_account_data'],
            'username' => ['form' => true, 'view' => false, 'referToBox' => 'box_dati_accesso'],
            'user_profile_age_group_id' => ['form' => true, 'view' => true, 'referToBox' => 'box_informazioni_base'],
            'user_profile_area_id' => ['form' => true, 'view' => false, 'referToBox' => 'box_role_and_area'],
            'userProfileImage' => ['form' => true, 'view' => true, 'referToBox' => 'box_foto'],
            'user_profile_role_id' => ['form' => true, 'view' => false, 'referToBox' => 'box_role_and_area'],
        ]
    ]
];


$modules['attachments'] = [
    'class' => 'lispa\amos\attachments\FileModule',
	'statistics' => 'amos\statistics\statistics\attachments\AttachmentsStats',
    'webDir' => 'files',
    'tempPath' => '@common/uploads/temp',
    'storePath' => '@common/uploads/store',
    'cache_age' => '2592000',
];

$modules['community'] = [
    'class' => 'lispa\amos\community\AmosCommunity',
    'htmlMailContent' => [
        'change-role' => '@backend/mail/community/change-role',
        'invitation' => '@backend/mail/community/invitation'
    ],
    'showSubcommunities' => false,
    'showSubcommunitiesWidget' => true,
    'enableWizard' => false,
    'communityType' => \lispa\amos\community\models\CommunityType::COMMUNITY_TYPE_CLOSED,
    'bypassWorkflow' => true,
    'viewTabContents' => false,
    'extendRoles' => true,
    'customInvitationForm' => true,
    'inviteUserOfcommunityParent' => true,
    'disableCommunityAssociationUserProfile' => true,
    'communityRequiredFields' => ['name', 'community_type_id'],
    'hideCommunityTypeSearchFilter' => true,
];

$modules['cwh'] = [
    'class' => 'lispa\amos\cwh\AmosCwh',
    'rbacEnabled' => false,
    'validatoriEnabled' => false
];

$modules['email'] = [
    'class' => 'lispa\amos\emailmanager\AmosEmail',
    'templatePath' => '@common/mail/emails',
];

$modules['notify'] = [
    'class' => 'lispa\amos\notificationmanager\AmosNotify',
];

/*
  $modules['tag'] = [
  'class' => 'lispa\amos\tag\AmosTag',
  ];
 */

return $modules;
