<?php

$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/_sedit/autoloads/seditoperator.php',
                                    'class' => 'sEditOperator',
                                    'operator_names' => array( 'se_node', 'se_attribute', 'se_name' ) );

$eZTemplateFunctionArray = array();
$eZTemplateFunctionArray[] = array( 'function' => 'sObjectForwardInit',
                                    'function_names' => array( 'node_sedit_gui', 'attribute_sedit_gui' ) );
if ( !function_exists( 'sObjectForwardInit' ) )
{
    function sObjectForwardInit()
    {
    	$forward_rules = array(
                'node_sedit_gui' => array( 'template_root' => 'node/view',
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
                                          'use_views' => 'view' ),
                'attribute_sedit_gui' => array( 'template_root' => array( 'type' => 'multi_match',
                                                                         'attributes' => array( 'is_information_collector' ),
                                                                         'matches' => array( array( false,
                                                                                                    'content/datatype/view' ),
                                                                                             array( true,
                                                                                                    'content/datatype/collect' ) ) ),
                                               'render_mode' => false,
                                               'input_name' => 'attribute',
                                               'output_name' => 'attribute',
                                               'namespace' => 'ContentAttribute',
                                               'attribute_keys' => array( 'attribute_identifier' => array( 'contentclass_attribute_identifier' ),
                                                                          'attribute' => array( 'contentclassattribute_id' ),
                                                                          'class_identifier' => array( 'object', 'class_identifier' ),
                                                                          'class' => array( 'object', 'contentclass_id' ) ),
                                               'attribute_access' => array( array( 'view_template' ) ),
                                               'optional_views' => true,
                                               'use_views' => 'view' ),
                                           );
            return new sObjectForwarder( $forward_rules );
    }
}
?>
