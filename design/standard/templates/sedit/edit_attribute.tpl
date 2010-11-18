{default $view_parameters            = array()
         $attribute_categorys        = ezini( 'ClassAttributeSettings', 'CategoryList', 'content.ini' )
         $attribute_default_category = ezini( 'ClassAttributeSettings', 'DefaultCategory', 'content.ini' )}

{foreach $content_attributes_grouped_data_map as $attribute_group => $content_attributes_grouped}

{foreach $content_attributes_grouped as $attribute_identifier => $attribute}
{def $contentclass_attribute = $attribute.contentclass_attribute}
<div class="block ezcca-edit-datatype-{$attribute.data_type_string} ezcca-edit-{$attribute_identifier}">
    {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
    <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
</div>
{undef $contentclass_attribute}
{/foreach}

{/foreach}

{/default}
