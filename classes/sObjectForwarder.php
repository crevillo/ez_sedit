<?php

class sObjectForwarder extends eZObjectForwarder
{
    function __construct( $rules )
    {
        $this->eZObjectForwarder($rules);
    }

    
    function process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )
    {
        if ( !isset( $this->Rules[$functionName] ) )
        {
            $tpl->undefinedFunction( $functionName );
            return;
        }
        
        // stevo
        $rule = $this->Rules[$functionName];
        $outCurrentNamespace = $currentNamespace;
        $input_name = $rule["input_name"];
        if ( isset( $rule['namespace'] ) )
        {
            $ruleNamespace = $rule['namespace'];
            if ( $ruleNamespace != '' )
            {
                if ( $outCurrentNamespace != '' )
                    $outCurrentNamespace .= ':' . $ruleNamespace;
                else
                    $outCurrentNamespace = $ruleNamespace;
            }
        }
        
        $params = $functionParameters;
        if ( !isset( $params[$input_name] ) )
        {
            $tpl->missingParameter( $functionName, $input_name );
            return;
        }

        $old_nspace = $rootNamespace;

        $input_var = $tpl->elementValue( $params[$input_name], $rootNamespace, $currentNamespace, $functionPlacement );
        if ( !is_object( $input_var ) )
        {
            $tpl->warning( $functionName, "Parameter $input_name is not an object", $functionPlacement );
            return;
        }
        
        $view_mode = "";
        $view_dir = "";
        $view_var = null;
        $renderMode = false;
        if ( isset( $rule["render_mode"] ) )
        {
            $renderMode = $rule["render_mode"];
        }
        if ( isset( $params['render-mode'] ) )
        {
            $renderMode = $tpl->elementValue( $params['render-mode'], $rootNamespace, $currentNamespace, $functionPlacement );
        }
        if ( $renderMode )
            $view_dir .= "/render-$renderMode";
        if ( $rule["use_views"] )
        {
            $view_var = $rule["use_views"];
            if ( !isset( $params[$view_var] ) )
            {
                if ( !isset( $rule['optional_views'] ) or
                     !$rule['optional_views'] )
                    $tpl->warning( $functionName, "No view specified, skipping views" );
            }
            else
            {
                $view_mode = $tpl->elementValue( $params[$view_var], $rootNamespace, $currentNamespace, $functionPlacement );
                $view_dir .= "/" . $view_mode;
            }
        }
        
        $resourceKeys = false;
        if ( isset( $rule['attribute_keys'] ) )
        {
            $resourceKeys = array();
            $attributeKeys = $rule['attribute_keys'];
            foreach( $attributeKeys as $attributeKey => $attributeSelection )
            {
                $keyValue = $tpl->variableAttribute( $input_var, $attributeSelection );
                $resourceKeys[] = array( $attributeKey, $keyValue );
            }
        }

        $triedFiles = array();
        $extraParameters = array();
        if ( $resourceKeys !== false )
            $extraParameters['ezdesign:keys'] = $resourceKeys;
        
        $resourceData = $tpl->loadURIRoot( "design:sedit/${functionName}_start.tpl", false, $extraParameters );
        $sub_text = "";
        $setVariableArray = array();
        $tpl->setVariable( $rule["output_name"], $input_var, $outCurrentNamespace );
        $setVariableArray[] = $rule["output_name"];
        // Set design keys
        //$tpl->setVariable( 'used', $designUsedKeys, $designKeyNamespace );
        //$tpl->setVariable( 'matched', $designMatchedKeys, $designKeyNamespace );
        // Set function parameters
        foreach ( array_keys( $params ) as $paramName )
        {
            if ( $paramName == $input_name or
                 $paramName == $view_var )
            {
                continue;
            }
            $paramValue = $tpl->elementValue( $params[$paramName], $old_nspace, $currentNamespace, $functionPlacement );
            $tpl->setVariable( $paramName, $paramValue, $outCurrentNamespace );
            $setVariableArray[] = $paramName;
        }
        // Set constant variables
        if ( isset( $rule['constant_template_variables'] ) )
        {
            foreach ( $rule['constant_template_variables'] as $constantTemplateVariableKey => $constantTemplateVariableValue )
            {
                if ( $constantTemplateVariableKey == $input_name or
                     $constantTemplateVariableKey == $view_var or
                     $tpl->hasVariable( $constantTemplateVariableKey, $currentNamespace ) )
                    continue;
                $tpl->setVariable( $constantTemplateVariableKey, $constantTemplateVariableValue, $outCurrentNamespace );
                $setVariableArray[] = $constantTemplateVariableKey;
            }
        }
        
        
        $root = $resourceData['root-node'];
        $tpl->process( $root, $sub_text, $outCurrentNamespace, $outCurrentNamespace );
        $textElements[] = $sub_text;
        
        foreach ( $setVariableArray as $setVariableName )
        {
            $tpl->unsetVariable( $setVariableName, $outCurrentNamespace );
        }
        
        parent::process( $tpl, $textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace );
        
        $resourceData = $tpl->loadURIRoot( "design:sedit/${functionName}_end.tpl", false, $extraParameters );
        $sub_text = "";
        $setVariableArray = array();
        $tpl->setVariable( $rule["output_name"], $input_var, $outCurrentNamespace );
        $setVariableArray[] = $rule["output_name"];
        // Set design keys
        //$tpl->setVariable( 'used', $designUsedKeys, $designKeyNamespace );
        //$tpl->setVariable( 'matched', $designMatchedKeys, $designKeyNamespace );
        // Set function parameters
        foreach ( array_keys( $params ) as $paramName )
        {
            if ( $paramName == $input_name or
                 $paramName == $view_var )
            {
                continue;
            }
            $paramValue = $tpl->elementValue( $params[$paramName], $old_nspace, $currentNamespace, $functionPlacement );
            $tpl->setVariable( $paramName, $paramValue, $outCurrentNamespace );
            $setVariableArray[] = $paramName;
        }
        // Set constant variables
        if ( isset( $rule['constant_template_variables'] ) )
        {
            foreach ( $rule['constant_template_variables'] as $constantTemplateVariableKey => $constantTemplateVariableValue )
            {
                if ( $constantTemplateVariableKey == $input_name or
                     $constantTemplateVariableKey == $view_var or
                     $tpl->hasVariable( $constantTemplateVariableKey, $currentNamespace ) )
                    continue;
                $tpl->setVariable( $constantTemplateVariableKey, $constantTemplateVariableValue, $outCurrentNamespace );
                $setVariableArray[] = $constantTemplateVariableKey;
            }
        }
        
        
        $root = $resourceData['root-node'];
        $tpl->process( $root, $sub_text, $outCurrentNamespace, $outCurrentNamespace );
        $textElements[] = $sub_text;
        
        foreach ( $setVariableArray as $setVariableName )
        {
            $tpl->unsetVariable( $setVariableName, $outCurrentNamespace );
        }
    }

    public $Rules;
};

?>
