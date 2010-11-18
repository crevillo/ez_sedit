<?php

function _sedit_ContentActionHandler( &$module, &$http, &$ObjectID ) 
{ 
   if ( $http->hasPostVariable("sEditAttributeAction") ) 
   {

    
       //$parameters = array($ObjectID, 'f', $http->postVariable('ContentObjectLanguageCode'));

    $res = eZTemplateDesignResource::instance();
    $res->setKeys( array( array( 'layout', 'print' ) ) );

       $content = eZModule::findModule('content');
       //$content->setCurrentAction( 'EditButton' );
       //$module->setCurrentAction( 'Publish', 'edit' );
       //$http->setPostVariable("EditButton", true);
       $obj = eZContentObject::fetch( $ObjectID );

       $FromLanguage = $EditLanguage = $http->postVariable("ContentObjectLanguageCode");

       if ( !$obj )
        return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

        // This controls if the final access check is done.
        // Some code will turn it off since they do the checking themselves.
        $isAccessChecked = false;
        $classID = $obj->attribute( 'contentclass_id' );
        $class = eZContentClass::fetch( $classID );

    if ( !$obj->canEdit( false, false, false, $EditLanguage ) )
    {
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel',
                                     array( 'AccessList' => $obj->accessList( 'edit' ) ) );
    }
    $isAccessChecked = true;


    // Check the new version against history limit for class $classID
    $versionlimit = eZContentClass::versionHistoryLimit( $classID );
    $versionCount = $obj->getVersionCount();
    if ( $versionCount < $versionlimit )
    {
        $version = $obj->createNewVersionIn( $EditLanguage, $FromLanguage, false, true, eZContentObjectVersion::STATUS_INTERNAL_DRAFT );
            return $content->run( 'edit', array( $ObjectID, $version->attribute( 'version' ), $EditLanguage ), array('TemplateName' => 'design:sedit/edit.tpl') );
    }
    else
    {
        $params = array( 'conditions'=> array( 'status' => eZContentObjectVersion::STATUS_ARCHIVED ) );
        $versions = $obj->versions( true, $params );
        if ( count( $versions ) > 0 )
        {
            $modified = $versions[0]->attribute( 'modified' );
            $removeVersion = $versions[0];
            foreach ( $versions as $version )
            {
                $currentModified = $version->attribute( 'modified' );
                if ( $currentModified < $modified )
                {
                    $modified = $currentModified;
                    $removeVersion = $version;
                }
            }

            $db = eZDB::instance();
            $db->begin();
            $removeVersion->removeThis();
            $version = $obj->createNewVersionIn( $EditLanguage, false, false, true, eZContentObjectVersion::STATUS_INTERNAL_DRAFT );
            $db->commit();

                return $content->run( 'edit', array( $ObjectID, $version->attribute( 'version' ), $EditLanguage ) );

            return eZModule::HOOK_STATUS_CANCEL_RUN;
        }
        else
        {
            $http->setSessionVariable( 'ExcessVersionHistoryLimit', true );
            $currentVersion = $obj->attribute( 'current_version' );
            $content->run( 'history', array( $ObjectID, $currentVersion, $EditLanguage ) );
            return eZModule::HOOK_STATUS_CANCEL_RUN;
        }
    }


       //return $content->run('edit', $parameters);

       
   }
} 

?>