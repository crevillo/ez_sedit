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
        switch ( $operatorName )
        {
            case 'se_node':
            {
                $node = $namedParameters['node'];
                $start = self::nodeStart($node);
                $end = self::nodeEnd();
                $operatorValue = $start . $operatorValue . $end;
            } break;
            case 'se_attribute':
            {
                $attribute = $namedParameters['attribute'];
                $start = self::attributeStart($attribute);
                $end = self::attributeEnd();
                $operatorValue = $start . $operatorValue . $end;
            } break;
            case 'se_name':
            {
                $object = $node->object();
                $dataMap = $object->fetchDataMap();
                $namePattern = $object->contentClass()->ContentObjectName();
                $namePattern = implode(',', explode('<', $namePattern));
                $namePattern = implode(',', explode('|', $namePattern));
                $namePattern = implode(',', explode('>', $namePattern));
                $namePatternArray = explode(',', $namePattern);
                $ops = new eZTemplateStringOperator();
                foreach ( $namePatternArray as $nameIdentifier ) {
                    if ( $nameIdentifier != '' ) {
                        $attribute = $dataMap['$nameIdentifier'];
                        $name = $attribute->content();
                        if ( $ops->wash($name, false) == $operatorValue ) {
                            $start = self::attributeStart($attribute);
                            $end = self::attributeEnd();
                            $operatorValue = $start . $operatorValue . $end;
                            break;
                        }
                    }
                }
            } break;
        }

    }

    static private function nodeStart($node) {
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'node', $node );
        return $tpl->fetch( 'design:sedit/node_sedit_gui_start.tpl' );
    }

    static private function nodeEnd() {
        $tpl = eZTemplate::factory();
        return $tpl->fetch( 'design:sedit/node_sedit_gui_end.tpl' );
    }

    static private function attributeStart($attribute) {
        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'attribute', $attribute );
        return $tpl->fetch( 'design:sedit/attribute_sedit_gui_start.tpl' );
        
    }

    static private function attributeEnd() {
        $tpl = eZTemplate::factory();
        return $tpl->fetch( 'design:sedit/attribute_sedit_gui_end.tpl' );
    }

    /// \privatesection
    
    /// \return The class variable 'Operators' which contains an array of available operators names.
    var $Operators;

    /// \privatesection
    /// \return The class variable 'Debug' to false.
    var $Debug;
    
    
}

?>
