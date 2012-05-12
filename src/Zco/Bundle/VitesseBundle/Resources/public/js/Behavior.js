/**
 * @provides vitesse-behavior
 */
var Behavior = {
	behaviors: {},
	statics: {},
	initialized: {},
	
	create: function(name, control_function)
	{
		this.behaviors[name] = control_function;
		this.statics[name] = {};
	},

	init: function(map)
	{
		var missing_behaviors = [];
		for (var name in map)
		{
			if (!(name in this.behaviors))
			{
				missing_behaviors.push(name);
				continue;
			}
			
			var configs = map[name];
			if (!configs.length)
			{
				if (initialized.hasOwnProperty(name))
				{
					continue;
				}
				configs = [null];
			}
			for (var ii = 0; ii < configs.length; ii++)
			{
				this.behaviors[name](configs[ii], this.statics[name]);
			}
			this.initialized[name] = true;
		}
		
		if (missing_behaviors.length)
		{
			throw new Error(
				'Behavior.init(map): behavior(s) not registered: ' +
				missing_behaviors.join(', ')
			);
		}
	}
};