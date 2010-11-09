<?php

class sEditOperator
{
    function __construct()
    {
		$this->Operators = array( 'se_node', 'se_attribute', 'se_name' );
        $this->Debug = false;
    }

    function operatorList()
    {
        return $this->Operators;
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'se_node' => array('content' => array( 'type' => 'string',
                                                             'required' => true ),
                                         'node' => array( 'type' => 'object',
                                                             'required' => true )),
                      'se_attribute' => array('content' => array( 'type' => 'string',
                                                             'required' => true ),
                                         'attribute' => array( 'type' => 'object',
                                                             'required' => true )),
                      'se_name' => array('content' => array( 'type' => 'string',
                                                             'required' => true ),
                                         'node' => array( 'type' => 'object',
                                                             'required' => true ))
					);
    }

    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        $startTpl = eZTemplate::factory();
        $endTpl = eZTemplate::factory();
        $start = '';
        $end = '';

        switch ( $operatorName )
        {
            case 'se_node':
            {
                $node = $namedParameters['node'];
                $startTpl = $tpl->setVariable( 'node', $node );
                $start = $startTpl->fetch( 'design:sedit/node_sedit_gui_start.tpl' );
                $end = $endTpl->fetch( 'design:sedit/node_sedit_gui_end.tpl' );
            } break;
            case 'se_attribute':
            {
                $attribute = $namedParameters['attribute'];
                $startTpl = $tpl->setVariable( 'attribute', $attribute );
                $start = $startTpl->fetch( 'design:sedit/attribute_sedit_gui_start.tpl' );
                $end = $endTpl->fetch( 'design:sedit/attribute_sedit_gui_end.tpl' );
            } break;
            case 'se_name':
            {
                // @TODO: work out which attribute is used to generate the node name +
                // use attribute_sedit_gui for this
            } break;
        }

        $operatorValue = $start . $operatorValue . $end;
    }

    /// \privatesection
    
    /// \return The class variable 'Operators' which contains an array of available operators names.
    var $Operators;

    /// \privatesection
    /// \return The class variable 'Debug' to false.
    var $Debug;
    
    
}

?>
