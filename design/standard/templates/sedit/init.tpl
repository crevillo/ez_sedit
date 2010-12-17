<script type="text/javascript">
{literal}

  YUI({
    modules: {
      'sedit': {
        fullpath: '{/literal}{'javascript/sedit.js'|ezdesign(no)}{literal}',
        requires: ['node', 'event', 'dom', 'io-form', 'querystring-stringify-simple', 'anim']
      }
    }
  }).use('sedit', function(Y){
    var policies = 
{/literal}
    {def $perms=fetch( 'user', 'user_role', hash( 'user_id', $current_user.contentobject_id ) )
       $contentPerms=array()
       $nolimits=false
       $currentNodeId=0
       $enableNodeFunctions=0
       $enableAttributeFunctions=0}

    {if is_set($current_node) }
      {set $currentNodeId=$current_node.node_id}
    {/if}

    {if ezini_hasvariable('NodeFunctions', 'sEditSettings', 'sedit.ini',,true() )}
      {set $enableNodeFunctions=cond(ezini('NodeFunctions', 'sEditSettings', 'sedit.ini',,true() ), 1, 0) }
    {/if}
    {if ezini_hasvariable('AttributeFunctions', 'sEditSettings', 'sedit.ini',,true() )}
      {set $enableAttributeFunctions=cond(ezini('AttributeFunctions', 'sEditSettings', 'sedit.ini',,true() ), 1, 0) }
    {/if}
     
    {foreach $perms as $policy}
      {if or( $policy.moduleName|eq('*'), and( $policy.moduleName|eq('content'), $policy.functionName|eq('*') ) ) }
        {set $nolimits=true}
      {/if}
      {if $policy.moduleName|eq('content') }
        {set $contentPerms=$contentPerms|append($policy)}
      {/if}
    {/foreach}
    {if $nolimits|eq(true)}
      '*'
    {else}{ldelim}
      {foreach $contentPerms as $policy}
        {$policy.functionName}:
          {if $policy.limitation|is_array}
            {ldelim}{foreach $policy.limitation as $limit}
              {$limit.identifier}:
                [{foreach $limit.values_as_array as $value}
                  '{$value}'{delimiter},{/delimiter}
                {/foreach}]{delimiter},{/delimiter}
            {/foreach}{rdelim}
          {else}
            '*'
          {/if}{delimiter},{/delimiter}
      {/foreach}{rdelim}
    {/if}
{literal};
    Y.sEdit.I18N = {
      node_edit: '{/literal}{'Edit'|i18n('design/standard/sedit/node')}{literal}',
      node_move: '{/literal}{'Move'|i18n('design/standard/sedit/node')}{literal}',
      node_remove: '{/literal}{'Remove'|i18n('design/standard/sedit/node')}{literal}',
      node_hide: '{/literal}{'Hide'|i18n('design/standard/sedit/node')}{literal}',
      node_sort: '{/literal}{'Sort'|i18n('design/standard/sedit/node')}{literal}',
      node_addlocations: '{/literal}{'Add locations'|i18n('design/standard/sedit/node')}{literal}',
      node_pushtoblock: '{/literal}{'Push to block'|i18n('design/standard/sedit/node')}{literal}',
      attribute_edit: '{/literal}{'Edit'|i18n('design/standard/sedit/attribute')}{literal}',
      attribute_publish: '{/literal}{'Publish'|i18n('design/standard/sedit/attribute')}{literal}',
      attribute_cancel: '{/literal}{'Cancel'|i18n('design/standard/sedit/attribute')}{literal}'
    }
    Y.on('domready', function(){
      Y.sEdit.init({
        policies: policies,
        ezRoot: '{/literal}{''|ezurl(no)}{literal}',
        userId: '{/literal}{$current_user.contentobject_id}{literal}',
        currentNodeId: '{/literal}{$currentNodeId}{literal}',
        enableNodeFunctions: '{/literal}{$enableNodeFunctions}{literal}',
        enableAttributeFunctions: '{/literal}{$enableAttributeFunctions}{literal}'
      });
    })
  });
{/literal}

</script>