<div class="ezwt-right">
	<p><a class="se-toggle" href="#" title="{'Edit content inline'|i18n('extension/_sedit')}">Edit content inline</a></p>
</div>
<script type="text/javascript">
{literal}

	YUI({
		modules: {
			'sedit': {
			fullpath: '{/literal}{'javascript/sedit.js'|ezdesign}{literal}',
			requires: ['node', 'event', 'dom']
		}
	  }
	}).use('sedit', function(Y){
		var policies = 
{/literal}
		{def $perms=fetch( 'user', 'user_role', hash( 'user_id', $current_user.contentobject_id ) )
			 $contentPerms=array()
			 $nolimits=false}
		 
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
			edit: 'Edit'
		}
		Y.on('domready', function(){
			Y.sEdit.init({
				policies: policies,
				userId: '{/literal}{$current_user.contentobject_id}{literal}'
			});
		})
	});
{/literal}

</script>