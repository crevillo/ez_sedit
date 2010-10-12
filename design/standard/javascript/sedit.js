YUI.add('sedit', function(Y){
	var L = Y.Lang,
		_attributeCache = {},
		_funcs = ['edit', 'hide'], //, 'move'],
		_nodeIcons = {},
		_nodeActions = {},
		_nodeControls, _currentItem, _policies, _userId;

	var _I18N = {
		edit: 'Edit',
		hide: 'Hide',
		move: 'Move'
	};
		
	_nodeActions = {
		edit: function(node, atts) {
			console.info('/content/edit/' + atts.nid);
			//window.location.href = '/content/edit/' + atts.nid;
		},
		move: function(node, atts) {
		},
		hide: function(node, atts) {
		},
		remove: function(node, atts) {
		},
		manage_locations: function(node, atts) {
		},
		translate: function(node, atts) {
		}
	};
	
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
		
		for ( i=0, l=_funcs.length; i<l; i++ ) {
			attributes[_funcs[i]] = _canDo(_funcs[i], attributes);
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
	
	function _canDoAll_funcs() {
		if ( _policies == '*' ) {
			return true;
		}
		
		return false;
	}
	
	function _canDoAll(funcName) {
		if ( _canDoAll_funcs() ) {
			return true;
		}
		
		if ( !L.isObject(_policies) ) {
			return false;
		}
		
		var policy = _policies[funcName];
		
		if ( policy == '*' ) {
			return true;
		}
		
		return false;
	}
	
	function _canDo(funcName, node) {
		if ( _canDoAll(funcName) ) {
			return true;
		}
		
		var policy = _policies[funcName];
		
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
			if ( node.owid != _userId ) {
				return false;
			}
		}

		// TODO: Test for Group: Self, self and current session
		
		
		return true;
	}
	
	// returns true if the user can do at least one of the available content functions on a node
	function _canDoOne(node) {
		for ( i=0, l=_funcs.length; i<l; i++ ) {
			if ( node[_funcs[i]] == true ) {
				return true;
			}
		}
		return false;
	}

	
	function _uiSetVisible(node) {
		var attributes = _parseNode(node);

		if ( !_canDoOne(attributes) ) {
			return false;
		}
		
		for ( i=0, l=_funcs.length; i<l; i++ ) {
			if ( attributes[_funcs[i]] ) {
				_nodeIcons[_funcs[i]].addClass('on');
			} else {
				_nodeIcons[_funcs[i]].removeClass('on');
			}
		}

		if ( !!_currentItem ) {
			_uiSetInvisible(_currentItem);
		}

		node.append(_nodeControls);
		node.addClass('on');
		_nodeControls.addClass('on');

		_currentItem = node;
	}
	
	function _uiSetInvisible(node) {
		_nodeControls.removeClass('on');
		node.removeClass('on');

		_currentItem = null;
	}
	
	function _initUI() {
		var icon;
		_nodeControls = Y.Node.create('<div class="se-node-controls"></div>');
		
		for ( i=0, l=_funcs.length; i<l; i++ ) {
			icon = Y.Node.create('<a href="#" class="se-icon se-' + _funcs[i] + '" title="' + _I18N[_funcs[i]] + '">' + _I18N[_funcs[i]] + '</a>');
			_nodeControls.append(icon);
			icon.setData('funcName', _funcs[i]);
			Y.on('click', function(e){
				_nodeActions[e.target.getData('funcName')](_currentItem, _parseNode(_currentItem));
				e.preventDefault();
			}, icon);
			icon.setData('funcName', _funcs[i]);
			_nodeIcons[_funcs[i]] = icon;
		}

		Y.get('body').append(_nodeControls);
	}
	
	function _addListeners() {
		Y.delegate('mouseenter', function(e) {
			_uiSetVisible(e.currentTarget);
		}, 'body', '.se-node');
		
		Y.delegate('mouseleave', function(e) {
			_uiSetInvisible(e.currentTarget);
		}, 'body', '.se-node');
	}
	
	function _init(config) {
		console.info(_I18N);
		_policies = config.policies;
		_userId = config.userId;
		_initUI();
		_addListeners();
	}
	
	Y.sEdit = {
		I18N: _I18N,
		init: _init
	}


}, '1', {requires: ['node', 'event', 'dom']});