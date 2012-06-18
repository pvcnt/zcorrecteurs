/**
 * @provides javelin-metadata
 */
var Metadata = {
	data: {},
	
	mergeData : function(block, data)
	{
		this.data[block] = Object.clone(data);
	},
	
	getData: function(element)
	{
		var meta_id = (element.get('data-meta') || '').split('_');
		
		if (meta_id[0] && meta_id[1])
		{
			var block = this.data[meta_id[0]];
			var index = meta_id[1];
			if (block && (index in block))
			{
				return block[index];
			}
			else
			{
		  		throw new Error(
					'JX.Stratcom.getData(<element>): Tried to access data (block ' +
					meta_id[0] + ', index ' + index + ') that was not present. This ' +
					'probably means you are calling getData() before the block ' +
					'is provided by mergeData().'
				);
			}
		}
		
		return {};
	},
	
	addData: function(element, data)
	{
		if (!this.data[1]) //1 is a the block for Javascript
		{
			this.data[1] = {};
		}
		var index = Object.getLength(this.data[1]);
		this.data[1][index] = Object.clone(data);
		
		element.set('data-meta', '1_' + index);
	}
};