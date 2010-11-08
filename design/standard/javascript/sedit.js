YUI.add('sedit', function(Y){
	var L = Y.Lang,
		_attributeCache = {},
		_nodeFunctions = ['edit', 'move', 'remove', 'hide', 'addlocations'],
		_attributeFunctions = ['edit'],
		_nodeIcons = {},
		_nodeActions = {},
		_attributeActions = {},
		_nodeControls, _attributeControls, _currentNodeItem, _currentAttributeItem;

	var _I18N = {
		edit: 'Edit',
		hide: 'Hide',
		move: 'Move',
		remove: 'Remove',
		addlocations: 'Add locations'
	};
		
	_nodeActions = {
		edit: function(node, atts) {
			_postRequest('/content/action', {
				ContentObjectLanguageCode: atts.lang,
				ContentNodeID: atts.nid,
				NodeID: atts.nid,
				ContentObjectID: atts.oid,
				RedirectURIAfterPublish: window.location.href,
				RedirectIfDiscarded: window.location.href,
				EditButton: true
			});
		},
		move: function(node, atts) {
			_postRequest('/content/action', {
				ContentNodeID: atts.nid,
				NodeID: atts.nid,
				MoveNodeButton: true
			});
		},
		hide: function(node, atts) {
			window.location.href = '/content/hide/' + atts.nid;
		},
		remove: function(node, atts) {
			_postRequest('/content/action', {
				ContentNodeID: atts.nid,
				NodeID: atts.nid,
				ContentObjectID: atts.oid,
				ActionRemove: true
			});
		},
		addlocations: function(node, atts) {
			_postRequest('/content/action', {
				ContentNodeID: atts.nid,
				NodeID: atts.nid,
				ContentObjectID: atts.oid,
				AddAssignmentButton: true
			});
		},
		translate: function(node, atts) {
		}
	};

	_attributeActions = {
		edit: function(node, atts) {
			console.info(atts);
		}
	}

	function _postRequest(url, params) {
		var form = document.createElement("form");
	    form.setAttribute("method", 'post');
	    form.setAttribute("action", url);

	    for(var key in params) {
	        var hiddenField = document.createElement("input");
	        hiddenField.setAttribute("type", "hidden");
	        hiddenField.setAttribute("name", key);
	        hiddenField.setAttribute("value", params[key]);

	        form.appendChild(hiddenField);
	    }

	    document.body.appendChild(form);
	    form.submit();
	}
	
	// 
	function _parseNode(node) {
		var classNames = node.getAttribute('className').split(' '),
			attributes = {},
			i, nameValue, name, value, l = classNames.length;
		
		// already been parsed - return cached attributes
		if ( !!_attributeCache[node._yuid] )
			return _attributeCache[node._yuid];
		
		for ( i=0; i<l; i++ ) {
			if ( classNames[i].indexOf('se-') === 0 ) {
				nameValue = classNames[i].substring(3);
				if ( nameValue.indexOf('-') > 0 ) {
					name = nameValue.substring(0, nameValue.indexOf('-'));
					value = nameValue.substring(nameValue.indexOf('-')+1);
					attributes[name] = value;
				}
			}
		}
		
		for ( i=0, l=_nodeFunctions.length; i<l; i++ ) {
			attributes[_nodeFunctions[i]] = _canDo(_nodeFunctions[i], attributes);
		}
		_attributeCache[node._yuid] = attributes;
		return attributes;
	}
	
	function _contains(arr, value) {
		var i, l=arr.length;
		for ( i=0; i<l; i++) {
			if ( arr[i] == value ) {
				return true;
			}
		}
		return false;
	}
	
	function _containsStartOf(arr, value) {
		var i, l=arr.length;
		for ( i=0; i<l; i++) {
			if ( value.indexOf(arr[i]) === 0 ) {
				return true;
			}
		}
		return false;
	}
	
	function _canDoAll_nodeFunctions() {
		if ( _config.policies == '*' ) {
			return true;
		}
		
		return false;
	}
	
	function _canDoAll(funcName) {
		if ( _canDoAll_nodeFunctions() ) {
			return true;
		}
		
		if ( !L.isObject(_config.policies) ) {
			return false;
		}
		
		var policy = _config.policies[funcName];
		
		if ( policy == '*' ) {
			return true;
		}
		
		return false;
	}
	
	function _canDo(funcName, node) {
		if ( _canDoAll(funcName) ) {
			return true;
		}
		
		var policy = _config.policies[funcName];
		
		if ( !L.isObject(policy) ) {
			return false;
		}

		if ( !L.isUndefined(policy.Node) && L.isArray(policy.Node) ) {
			if ( !_contains(policy.Node, node.nid) ) {
				return false;
			}
		}
		
		if ( !L.isUndefined(policy.Class) && L.isArray(policy.Class) ) {
			if ( !_contains(policy.Class, node.cid) ) {
				return false;
			}
		}
		
		if ( !L.isUndefined(policy.Section) && L.isArray(policy.Section) ) {
			if ( !_contains(policy.Section, node.sid) ) {
				return false;
			}
		}
		
		if ( !L.isUndefined(policy.Subtree) && L.isArray(policy.Subtree) ) {
			if ( !_containsStartOf(policy.Subtree, '/' + node.stid.split('-').join('/')) ) {
				return false;
			}
		}
		
		if ( !L.isUndefined(policy.Language) && L.isArray(policy.Language) ) {
			if ( !_contains(policy.Language, node.lang) ) {
				return false;
			}
		}
		
		if ( !L.isUndefined(policy.Owner) && ( policy.Owner == '1' || policy.Owner == '2' ) ) {
			if ( node.owid != _config.userId ) {
				return false;
			}
		}

		// TODO: Test for Group: Self, self and current session
		
		
		return true;
	}
	
	// returns true if the user can do at least one of the available content functions on a node
	function _canDoOne(node) {
		for ( i=0, l=_nodeFunctions.length; i<l; i++ ) {
			if ( node[_nodeFunctions[i]] == true ) {
				return true;
			}
		}
		return false;
	}

	
	function _nodeUISetVisible(node) {
		var attributes = _parseNode(node);
		console.info('Node visible: ' + attributes.oid);

		if ( !_canDoOne(attributes) ) {
			return false;
		}
		
		for ( i=0, l=_nodeFunctions.length; i<l; i++ ) {
			if ( attributes[_nodeFunctions[i]] ) {
				_nodeIcons[_nodeFunctions[i]].addClass('on');
			} else {
				_nodeIcons[_nodeFunctions[i]].removeClass('on');
			}
		}

		if ( !!_currentNodeItem ) {
			_nodeUISetInvisible(_currentNodeItem);
		}

		node.append(_nodeControls);
		node.addClass('on');
		_nodeControls.addClass('on');

		_currentNodeItem = node;
	}
	
	function _nodeUISetInvisible(node) {
		_nodeControls.removeClass('on');
		node.removeClass('on');

		var attributes = _parseNode(node);
		console.info('Node invisible: ' + attributes.oid);
		_currentNodeItem = null;
	}


	function _attributeUISetVisible(node) {
		var attributes = _parseNode(node);

		console.info('Att visible: ' + attributes.oid);

		if ( !_canDo('edit', attributes) ) {
			return false;
		}

		if ( !!_currentAttributeItem ) {
			_attributeUISetInvisible(_currentAttributeItem);
		}

		node.append(_attributeControls);
		node.addClass('on');
		_attributeControls.addClass('on');

		_currentAttributeItem = node;

		if ( !node.hasClass('se-node') ) {
			if ( !node.ancestor('.se-node.se-oid-' + attributes.oid) ) {
				node.addClass('se-node');
				_nodeUISetVisible(node);
			}
		}
	}
	
	function _attributeUISetInvisible(node) {
		_attributeControls.removeClass('on');
		node.removeClass('on');

		_currentAttributeItem = null;

		var attributes = _parseNode(node);

		console.info('Att invisible: ' + attributes.oid);
		/*if ( !node.ancestor('.se-node.se-oid-' + attributes.oid) ) {
			_nodeUISetInvisible(node);
		}*/
	}
	
	function _initUI() {
		var icon;

		if ( enableNodeFunctions ) {
			_nodeControls = Y.Node.create('<div class="se-node-controls"></div>');
			
			for ( i=0, l=_nodeFunctions.length; i<l; i++ ) {
				icon = Y.Node.create('<a href="#" class="se-icon se-' + _nodeFunctions[i] + '" title="' + _I18N['node_' + _nodeFunctions[i]] + '">' + _I18N[_nodeFunctions[i]] + '</a>');
				_nodeControls.append(icon);
				icon.setData('funcName', _nodeFunctions[i]);
				Y.on('click', function(e){
					_nodeActions[e.target.getData('funcName')](_currentNodeItem, _parseNode(_currentNodeItem));
					e.preventDefault();
				}, icon);
				icon.setData('funcName', _nodeFunctions[i]);
				_nodeIcons[_nodeFunctions[i]] = icon;
			}

			Y.get('body').append(_nodeControls);

			Y.delegate('mouseenter', function(e) {
				_nodeUISetVisible(e.currentTarget);
			}, 'body', '.se-node');
			
			Y.delegate('mouseleave', function(e) {
				_nodeUISetInvisible(e.currentTarget);
			}, 'body', '.se-node');
		}

		if ( enableAttributeFunctions ) {
			_attributeControls = Y.Node.create('<div class="se-attribute-controls"></div>');
			
			for ( i=0, l=_attributeFunctions.length; i<l; i++ ) {
				icon = Y.Node.create('<a href="#" class="se-icon se-' + _attributeFunctions[i] + '" title="' + _I18N['attribute_' + _attributeFunctions[i]] + '">' + _I18N[_attributeFunctions[i]] + '</a>');
				_attributeControls.append(icon);
				icon.setData('funcName', _attributeFunctions[i]);
				Y.on('click', function(e){
					_attributeActions[e.target.getData('funcName')](_currentAttributeItem, _parseNode(_currentAttributeItem));
					e.preventDefault();
				}, icon);
				icon.setData('funcName', _attributeFunctions[i]);
			}

			Y.get('body').append(_attributeControls);

			Y.delegate('mouseenter', function(e) {
				_attributeUISetVisible(e.currentTarget);
				e.stopPropagation();
			}, 'body', '.se-attribute');
			
			Y.delegate('mouseleave', function(e) {
				_attributeUISetInvisible(e.currentTarget);
				e.stopPropagation();
			}, 'body', '.se-attribute');
		}
	}
	
	function _init(config) {
		_config = config;
		_initUI();
	}
	
	Y.sEdit = {
		I18N: _I18N,
		init: _init
	}


}, '1', {requires: ['node', 'event', 'dom']});