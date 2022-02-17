<?php

require 'vendor/autoload.php';

session_start();

use Onetoweb\TrustedshopsV2\Client;
use Onetoweb\TrustedshopsV2\Token;

// params
$clientId = 'client_id';
$clientSecret = 'client_secret';

// get client
$client = new Client($clientId, $clientSecret);

// set update token callback
$client->setUpdateTokenCallback(function(Token $token) {
    
    //  store token
    $_SESSION['token'] = [
        'access_token' => $token->getAccessToken(),
        'expires' => $token->getExpires(),
    ];
    
});

if (isset($_SESSION['token'])) {
    
    // load token from storage
    $token = new Token(
        $_SESSION['token']['access_token'],
        $_SESSION['token']['expires']
    );
    
    $client->setToken($token);
}


/**
 * Examples api endpoints.
 */


/**
 * Get channels.
 * @see https://developers.etrusted.com/channels-api/channels-api.html#get-channels-by-token
 */
$channels = $client->get('/channels');

/**
 * Update a channel by ID.
 * @see https://developers.etrusted.com/channels-api/channels-api.html#put-channel-by-id
 */
$channelId = 'channel_id';
$channelId = $client->put("/channels/$channelId", [
    'name' => 'name',
    'address' => 'address'
]);

/**
 * Create a new event.
 * @see https://developers.etrusted.com/events-api/events-api.html#post--events
 */
$channelId = 'channel_id';
$result = $client->post('/events', [
    'type' => 'checkout',
    'defaultLocale' => 'nl_NL',
    'customer' => [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com'
    ],
    'channel' => [
        'id' => $channelId,
        'type' => 'etrusted'
    ],
    'transaction' => [
        'reference' => 'order-12345'
    ],
    'estimatedDeliveryDate' => '2017-01-07',
    'products' => [
        [
            'gtin' => '1234567890123',
            'imageUrl' => 'https://www.specialbrandshop.com/article123-TS-WH-M/image.jpg',
            'name' => 'Specialbrand T-Shirt White M',
            'mpn' => '23687778',
            'sku' => '1234-TS-WH-M',
            'brand' => 'specialbrand',
            'url' => 'https://www.specialbrandshop.com/article123-TS-WH-M/'
        ]
    ],
    'system' => 'customer_system_name',
    'systemVersion' => '1.0'
]);

/**
 * Get an event.
 * @see https://developers.etrusted.com/events-api/events-api.html#get--events-_eventRef_
 */
$eventRef = 'event_ref';
$event = $client->get("/events/$eventRef");

/**
 * Get event types.
 * @see https://developers.etrusted.com/event-types-api/event-types-api.html#get-event-types
 */
$eventTypes = $client->get('/event-types');

/**
 * Create an event type.
 * @see https://developers.etrusted.com/event-types-api/event-types-api.html#create-event-type
 */
$eventType = $client->post('/event-types', [
    'active' => true,
    'name' => 'event_type_name'
]);

/**
 * Get an event type by ID.
 * @see https://developers.etrusted.com/event-types-api/event-types-api.html#get-event-type
 */
$eventTypeId = 'event_type_id';
$eventType = $client->get("/event-types/$eventTypeId");

/**
 * Update an event type by ID.
 * @see https://developers.etrusted.com/event-types-api/event-types-api.html#put-event-type
 */
$eventTypeId = 'event_type_id';
$eventType = $client->put("/event-types/$eventTypeId", [
    'active' => false,
]);

/**
 * Delete an event type by ID.
 * @see https://developers.etrusted.com/event-types-api/event-types-api.html#delete-event-type
 */
$eventTypeId = 'event_type_id';
$client->delete("/event-types/$eventTypeId");

/**
 * Get a list of invites.
 * @see https://developers.etrusted.com/invites-api/invites-api.html#get-invite-list
 */
$channelId = 'channel_id';
$channelInvites = $client->get("/channels/$channelId/invites", [
    'count' => 10
]);

/**
 * Schedule new invites.
 * @see https://developers.etrusted.com/invites-api/invites-api.html#post--invites
 */
$channelId = 'channel_id';
$templateId = 'template_id';
$questionnaireTemplateId = 'questionnaire_template_id';

$result = $client->post('/invites', [
    'channel' => [
        'id' => $channelId,
        'type' => 'etrusted'
    ],
    'system' => 'customer_system_name',
    'systemVersion' => '1.0',
    'invites' => [[
        'questionnaireTemplate' => [
            'id' => $questionnaireTemplateId,
        ],
        'template' => [
            'id' => $templateId,
        ],
        'customer' => [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
        ],
        'transaction' => [
            'reference' => 'ORDER-121416990',
            'date' => '2017-11-01T13:30:15Z'
        ],
    ]]
]);

/**
 * Get invite rules
 * @see https://developers.etrusted.com/invite-rules-api/invite-rules-api.html#get-invite-rules
 */
$inviteRules = $client->get('/invite-rules');

/**
 * Create a new invite rule.
 * @see https://developers.etrusted.com/invite-rules-api/invite-rules-api.html#post-invite-rule
 */
$eventTypeId = 'event_type_id';
$templateId = 'template_id';
$inviteRule = $client->post('/invite-rules', [
    'name' => 'invite_rule_name',
    'eventTypeRef' => $eventTypeId,
    'templateRef' => $templateId,
    'sendingDelay' => 'P3D',
    'timeOfDay' => '10:00:00Z',
    'active' => true
]);

/**
 * Get an invite rule by ID.
 * @see https://developers.etrusted.com/invite-rules-api/invite-rules-api.html#get-invite-rule
 */
$inviteRuleId = 'invite_rule_id';
$inviteRule = $client->get("/invite-rules/$inviteRuleId");

/**
 * Update an invite rule by ID.
 * @see https://developers.etrusted.com/invite-rules-api/invite-rules-api.html#put-invite-rule
 */
$inviteRuleId = 'invite_rule_id';
$inviteRule = $client->put("/invite-rules/$inviteRuleId", [
    'name' => 'invite_rule_name',
    'sendingDelay' => 'P3D',
    'timeOfDay' => '10:00:00Z',
]);

/**
 * Delete an invite rule by ID.
 * @see https://developers.etrusted.com/invite-rules-api/invite-rules-api.html#delete-invite-rule
 */
$inviteRuleId = 'invite_rule_id';
$inviteRule = $client->delete("/invite-rules/$inviteRuleId");

/**
 * Get invite rules by channel ID.
 * @see https://developers.etrusted.com/invite-rules-api/invite-rules-api.html#get-invite-rules-by-channel
 */
$channelId = 'channel_id';
$inviteRules = $client->get("/channels/$channelId/invite-rules");

/**
 * Update an invite rule by channel ID.
 * @see https://developers.etrusted.com/invite-rules-api/invite-rules-api.html#put-invite-rule-by-channel
 */
$channelId = 'channel_id';
$inviteRuleId = 'invite_rule_id';
$client->put("/channels/$channelId/invite-rules/$inviteRuleId", [
    'active' => false,
]);

/**
 * Get a list of reviews.
 * @see https://developers.etrusted.com/reviews-api/reviews-api.html#getReviews
 */
$reviews = $client->get('/reviews', [
    'count' => 10
]);

/**
 * Get a review by ID.
 * @see https://developers.etrusted.com/reviews-api/reviews-api.html#getReview
 */
$reviewId = 'review_id';
$review = $client->get("/reviews/$reviewId");

/**
 * Create a veto for a review.
 * @see https://developers.etrusted.com/reviews-api/reviews-api.html#createVeto
 */
$reviewId = 'review_id';
$client->post("/reviews/$reviewId/vetos", [
    'comment' => 'My veto comment.',
    'reason' => 'UNTRUTHFUL',
    'channelName' => 'My channel name.'
]);

/**
 * Get an aggregate service review rating by channel ID.
 * @see https://developers.etrusted.com/reviews-api/reviews-api.html#get-channel-service-reviews-aggregate-rating
 */
$channelId = 'channel_id';
$aggregateRating = $client->get("/channels/$channelId/service-reviews/aggregate-rating");

/**
 * Save a review reply.
 * @see https://developers.etrusted.com/reviews-api/reviews-api.html#saveReviewReply
 */
$reviewId = 'review_id';
$client->post("/reviews/$reviewId/reply", [
    'comment' => 'test',
    'sendNotification' => false
]);

/**
 * Delete a review reply.
 * @see https://developers.etrusted.com/reviews-api/reviews-api.html#deleteReviewReply
 */
$client->delete("/reviews/$reviewId/reply");

/**
 * Get a list of templates.
 * @see https://developers.etrusted.com/templates-api/templates-api.html#getAllTemplates
 */
$templates = $client->get('/templates');

/**
 * Get a template by ID.
 * @see https://developers.etrusted.com/templates-api/templates-api.html#getTemplate
 */
$templateId = 'template_id';
$template = $client->get("/templates/$templateId");

/**
 * Get a template by ID and locale.
 * @see https://developers.etrusted.com/templates-api/templates-api.html#getTemplateByLocale
 */
$templateId = 'template_id';
$locales = 'nl_NL';
$template = $client->get("/templates/$templateId/locales/$locales");

/**
 * Retrieve a questionnaire link.
 * @see https://developers.etrusted.com/questionnaire-api/questionnaire-api.html#post--questionnaire-links
 */
$channelId = 'channel_id';
$questionnaireTemplateId = 'questionnaire_template_id';
$questionnaireLinks = $client->post('/questionnaire-links', [
    'type' => 'sales',
    'questionnaireTemplate' => [
        'id' => $questionnaireTemplateId
    ],
    'customer' => [
        'id' => 'cst-xxxxxxxx-yyyy-xxxx-yyyy-xxxxxxxxxxxx',
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@example.com',
        'mobile': 49123456789,
        'address' => 'Anystr. 17, 12345'
    ],
    'channel' => [
        'id' => $channelId,
        'type' => 'etrusted'
    ],
    'transaction' => [
        'reference' => 'order-12345',
        'date' => '2017-01-01T13:30:15Z'
    ],
    'products' => [
        [
            'gtin' => '1234567890123',
            'imageUrl' => 'https://www.specialbrandshop.com/article123-TS-WH-M/image.jpg',
            'name' => 'Specialbrand T-Shirt White M',
            'mpn' => '23687778',
            'sku' => '1234-TS-WH-M',
            'brand' => 'specialbrand',
            'url' => 'https://www.specialbrandshop.com/article123-TS-WH-M/'
        ]
    ],
    'metadata' => [
        'metaKey1' => 'metaValue1',
        'metaKey2' => 'metaValue2'
    ],
    'system' => 'customer_system_name',
    'systemVersion' => '1.0'
]);