{def $aid=ezhttp('AttributeId', 'post')}

<form name="editform" id="editform" enctype="multipart/form-data" method="post" action={concat( '/content/edit/', $object.id, '/', $edit_version, '/', $edit_language|not|choose( concat( $edit_language, '/' ), '/' ), $is_translating_content|not|choose( concat( $from_language, '/' ), '' ) )|ezurl}>


{def $content_language = ezini( 'RegionalSettings', 'Locale' )}

    <div class="context-attributes">
{foreach $content_attributes as $attribute_identifier => $attribute}
{if $attribute.id|eq($aid)}
<div class="block ezcca-edit-datatype-{$attribute.data_type_string} ezcca-edit-{$attribute_identifier}">
    {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
    <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
</div>
{/if}
{/foreach}
    </div>

    <div class="buttonblock">
    <input id="seditAttributePublish" class="defaultbutton" type="submit" name="PublishButton" value="{'Send for publishing'|i18n( 'design/ezwebin/content/edit' )}" />
    <input id="seditAttributeDiscard" class="button" type="submit" name="DiscardButton" value="{'Discard draft'|i18n( 'design/ezwebin/content/edit' )}" />
    <input type="hidden" name="DiscardConfirm" value="0" />
    <input type="hidden" name="RedirectIfDiscarded" value="{if ezhttp_hasvariable( 'LastAccessesURI', 'session' )}{ezhttp( 'LastAccessesURI', 'session' )}{/if}" />
    <input type="hidden" name="RedirectURIAfterPublish" value="{if ezhttp_hasvariable( 'LastAccessesURI', 'session' )}{ezhttp( 'LastAccessesURI', 'session' )}{/if}" />
    <input type="hidden" name="CustomActionButton[]" value="" />
    </div>

</form>