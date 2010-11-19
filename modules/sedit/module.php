<?php

$ViewList = array();
$ViewList['edit'] = array(
    'functions' => array( 'edit or create' ),
    'default_navigation_part' => 'ezcontentnavigationpart',
    'ui_context' => 'edit',
    'single_post_actions' => array( 'PublishButton' => 'Publish',
                                    'DiscardButton' => 'Discard',
                                    ),
    'post_action_parameters' => array( 'EditLanguage' => array( 'SelectedLanguage' => 'EditSelectedLanguage' ),
                                       'FromLanguage' => array( 'FromLanguage' => 'FromLanguage' ),
                                       'TranslateLanguage' => array( 'SelectedLanguage' => 'EditSelectedLanguage' ),
                                       'UploadFileRelation' => array( 'UploadRelationLocation' => 'UploadRelationLocationChoice' ) ),
    'script' => 'edit.php',
    'params' => array( 'ClassAttributeID', 'ObjectID', 'EditVersion', 'EditLanguage', 'FromLanguage' ) );

?>