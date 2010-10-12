<?php

$eZTemplateFunctionArray = array();
$eZTemplateFunctionArray[] = array( 'function' => 'sObjectForwardInit',
                                    'function_names' => array( 'sedit_node_view_gui' ) );
if ( !function_exists( 'sObjectForwardInit' ) )
{
    function sObjectForwardInit()
    {
    	$forward_rules = array(
                'sedit_node_view_gui' => array( 'template_root' => 'node/view',
                                          'input_name' => 'content_node',
                                          'output_name' => 'node',
                                          'namespace' => 'NodeView',
                                          'constant_template_variables' => array( 'view_parameters' => array( 'offset' => 0 ) ),
                                          'attribute_keys' => array( 'node' => array( 'node_id' ),
                                                                     'object' => array( 'contentobject_id' ),
                                                                     'class' => array( 'object', 'contentclass_id' ),
                                                                     'section' => array( 'object', 'section_id' ),
                                                                     'class_identifier' => array( 'object', 'class_identifier' ),
                                                                     'class_group' => array( 'object', 'match_ingroup_id_list' ),
                                                                     'state' => array( 'object', 'state_id_array' ),
                                                                     'state_identifier' => array( 'object', 'state_identifier_array' ),
                                                                     'parent_node' => array( 'parent_node_id' ),
                                                                     'depth' => array( 'depth' ),
                                                                     'url_alias' => array( 'url_alias' ),
                                                                     'remote_id' => array( 'object', 'remote_id' ),
                                                                     'node_remote_id' => array( 'remote_id' ),
                                                                     'parent_class_identifier' => array( 'parent', 'class_identifier' ) ),
                                          'attribute_access' => array(),
                                          'use_views' => 'view' )
                                           );
            return new sObjectForwarder( $forward_rules );
    }
}
?>
