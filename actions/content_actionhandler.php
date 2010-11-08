<?php

function _sedit_ContentActionHandler( &$module, &$http, &$objectID ) 
{ 
   if( $http->hasPostVariable("sEditAttributeAction") ) 
   {
       return true;
   }
} 

?>