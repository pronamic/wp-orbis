orbis_plugins_script = function()
{
	var $    = jQuery,
		self = { };
	
	/**
	 * Initialize
	 */
	self.init = function()
	{
		self.$pluginsTable          = $('table.orbis-plugins');
		self.$installPluginButtons  = self.$pluginsTable.find('.orbis-install-plugin');
        self.$activatePluginButtons = self.$pluginsTable.find('.orbis-activate-plugin');
		
		self.$installPluginButtons.on('click', function(event){ self.onInstallPluginButtonClick(event); });
        self.$activatePluginButtons.on('click', function(event){ self.onActivatePluginButtonClick(event); });
	};
	
	/**
	 * Handle install plugin button click
	 * 
	 * @param event
	 */
	self.onInstallPluginButtonClick = function(event)
	{
		event.preventDefault();
		
		var $button = $(event.currentTarget);
		
		$.post(
			ajaxurl,
			{
				'action'     : 'orbis_install_plugin',
				'nonce'      : $button.data('nonce'),
				'plugin_slug': $button.data('pluginSlug')
			}
		)
		.done(function(data)
		{
			console.log(data);
			
//			if ( '1' === result ) {
//				$( button )
//					.html( a8c_developer_i18n.installed )
//					.nextAll( '.a8c-developer-action-result' )
//					.remove();
//
//				$(button).unbind('click').prop('disabled', true);
//			} else {
//				$( button )
//					.html( a8c_developer_i18n.ERROR )
//					.nextAll( '.a8c-developer-action-result' )
//					.remove();
//
//				$( button ).after( '<span class="a8c-developer-action-result error">' + result + '</span>' );
//			}
		})
		.fail(function(data)
		{
			console.log(data);
			
//			$( button )
//				.html( a8c_developer_i18n.ERROR )
//				.nextAll( '.a8c-developer-action-result' )
//				.remove();
//
//			$( button ).after( '<span class="a8c-developer-action-result error">' + response.statusText + ': ' + response.responseText + '</span>' );
		});
	};

    /**
     * Handle activate plugin button click
     *
     * @param event
     */
    self.onActivatePluginButtonClick = function(event)
    {
        event.preventDefault();

        var $button = $(event.currentTarget);

        $.post(
            ajaxurl,
            {
                'action'     : 'orbis_activate_plugin',
                'nonce'      : $button.data('nonce'),
                'plugin_slug': $button.data('pluginSlug')
            }
        )
        .done(function(data){ console.log(data); })
        .fail(function(data){ console.log(data); });
    };
	
	$(document).ready(function()
	{
		self.init();
	});
}();