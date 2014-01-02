orbis_plugins_script = function()
{
	var $    = jQuery,
		self = { };
	
	/**
	 * Initialize
	 */
	self.init = function()
	{
        self.strings = orbis_plugins_script_strings;

        if (typeof self.strings !== 'object')
        {
            self.strings = { };
        }

		self.$pluginsTable          = $('table.orbis-plugins');
		self.$installPluginButtons  = self.$pluginsTable.find('.orbis-install-plugin');
        self.$activatePluginButtons = self.$pluginsTable.find('.orbis-activate-plugin');

        self.$messageBox = $('.orbis-plugins-message');
		
		self.$pluginsTable.on('click', '.orbis-install-plugin', function(event){ self.onPluginButtonClick(event, 'install'); });
        self.$pluginsTable.on('click', '.orbis-activate-plugin', function(event){ self.onPluginButtonClick(event, 'activate'); });
	};
	
	/**
	 * Handle install plugin button click
	 * 
	 * @param event
     * @param action
	 */
	self.onPluginButtonClick = function(event, action)
	{
		event.preventDefault();
		
		var $button      = $(event.currentTarget),
            $loadingIcon = $button.closest('tr').find('.loading-icon');

        self.$installPluginButtons.attr('disabled', true);
        self.$activatePluginButtons.attr('disabled', true);

        $loadingIcon.addClass('loading');

		$.post(
			orbis.ajaxUrl,
			{
				'action'     : 'orbis_' + action + '_plugin',
				'nonce'      : $button.data('nonce'),
				'plugin_slug': $button.data('pluginSlug')
			}
		)
        .always(function() // The always function is executed before the done and error function, so it won't re-enable the buttons that have been disabled
        {
            self.$installPluginButtons.attr('disabled', false);
            self.$activatePluginButtons.attr('disabled', false);

            $loadingIcon.removeClass('loading');
        })
		.done(function(data)
		{
            var json = $.parseJSON(data);

            if (typeof json === 'object' &&
                typeof json.success === 'boolean' &&
                typeof json.message === 'string')
            {
                if (json.success)
                {
                    self.showAdminMessage(json.message, 1);

                    // Update button
                    if (typeof self.strings.active_button_text === 'string')
                    {
                        $button.attr('value', self.strings.active_button_text);

                        $button.removeClass('orbis-install-plugin');
                        $button.removeClass('orbis-activate-plugin');

                        $button.attr('disabled', true);
                    }
                }
                else
                {
                    // An error code of 5 or higher means that the installation succeeded, but activation failed
                    if (typeof json.error_code === 'number' &&
                        typeof self.strings.activate_button_text === 'string' &&
                        json.error_code >= 5)
                    {
                        $button.attr('value', self.strings.activate_button_text);

                        $button.removeClass('orbis-install-plugin');

                        $button.addClass('orbis-activate-plugin');
                    }

                    self.showAdminMessage(json.message, 2);
                }
            }
            else
            {
                if (typeof self.strings.error_message_unknown === 'string')
                {
                    self.showAdminMessage(self.strings.error_message_unknown, 2);
                }
            }
		})
		.fail(function(data)
		{
            var json = $.parseJSON(data);

            if (typeof json === 'object' &&
                typeof json.success === 'boolean' &&
                typeof json.message === 'string')
            {
                self.showAdminMessage(json.message, 2);
            }
            else
            {
                if (typeof self.strings.error_message_connection === 'string')
                {
                    self.showAdminMessage(self.strings.error_message_connection, 2);
                }
            }
		});
	};

    /**
     * Shows the passed message as an admin message
     *
     * The level parameter can be one of the followin integers:
     * 1: Message
     * 2: Error
     *
     * @param message
     * @param level   1 | 2
     */
    self.showAdminMessage = function(message, level)
    {
        var $messageBoxClone = self.$messageBox.clone(),
            messageClass;

        self.$messageBox.after($messageBoxClone);

        $messageBoxClone.html('<p>' + message + '</p>');

        switch(level)
        {
            case 2:
                messageClass = 'error';
                break;

            default:
                messageClass = 'updated';
        }

        $messageBoxClone.addClass(messageClass);

        $messageBoxClone.animate({ height: $messageBoxClone.find('p').outerHeight(true) }, 300, function()
        {
            setTimeout(function()
            {
                $messageBoxClone.animate({ height: 0 }, 300, function()
                {
                    $messageBoxClone.remove();
                });
            }, 10000)
        });
    }

    /**
     *
     */
	$(document).ready(function()
	{
		self.init();
	});
}();