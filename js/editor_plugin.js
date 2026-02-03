(function() {

	var icons = window.wphbHeadingIcons || null;
	var DEFAULT_LEVELS = [1, 2, 3, 4, 5, 6];
	var settings = window.wphbHeadingButtonsSettings || {};
	var i18n = window.wphbHeadingButtonsI18n || {};
	var BUTTON_CLASS = 'wphb-button';
	var CONTAINER_CLASS = 'wphb-container';
	var buttonControls = [];

	function getHeadingLabel(level) {
		if (i18n.headingLabels && i18n.headingLabels[level]) {
			return i18n.headingLabels[level];
		}
		return 'Heading ' + level;
	}

	function addClass(el, className) {
		if (!el) {
			return;
		}

		if (el.classList) {
			el.classList.add(className);
			return;
		}

		if (typeof el.className === 'string' && el.className.indexOf(className) === -1) {
			el.className += ' ' + className;
		}
	}

	function ensureContainerClass(control) {
		if (!control || !control.getEl) {
			return;
		}

		var el = control.getEl();
		if (!el) {
			return;
		}

		var parent = el.parentNode;
		while (parent && parent.nodeType === 1) {
			if (parent.classList && parent.classList.contains('mce-btn-group')) {
				addClass(parent, CONTAINER_CLASS);
				return;
			}
			if (typeof parent.className === 'string' && parent.className.indexOf('mce-btn-group') !== -1) {
				addClass(parent, CONTAINER_CLASS);
				return;
			}
			parent = parent.parentNode;
		}

		addClass(el, CONTAINER_CLASS);
	}

	function setButtonActive(control, isActive) {
		if (!control) {
			return;
		}

		if (typeof control.active === 'function') {
			control.active(isActive);
			return;
		}

		if (typeof control.setActive === 'function') {
			control.setActive(isActive);
		}
	}

	function updateButtonState(editor, control, level, element) {
		if (!editor || !editor.dom) {
			return;
		}

		var target = element || (editor.selection ? editor.selection.getNode() : null);
		var selector = 'h' + level;
		var isActive = !!(target && editor.dom.getParent(target, selector));

		setButtonActive(control, isActive);
	}

	function updateAllButtonStates(editor, element) {
		buttonControls.forEach(function(entry) {
			updateButtonState(editor, entry.control, entry.level, element);
		});
	}

	function normalizeLevels(levels) {
		if (!Array.isArray(levels)) {
			return [];
		}

		var normalized = [];
		levels.forEach(function(level) {
			var parsed = parseInt(level, 10);
			if (parsed >= 1 && parsed <= 6 && normalized.indexOf(parsed) === -1) {
				normalized.push(parsed);
			}
		});

		normalized.sort(function(a, b) {
			return a - b;
		});

		return normalized;
	}

	var enabledLevels = Array.isArray(settings.levels) ? normalizeLevels(settings.levels) : DEFAULT_LEVELS.slice();

	function addHeadingButton(editor, level) {
		var button = {
			title: getHeadingLabel(level),
			classes: BUTTON_CLASS,
			onclick: function() {
				editor.execCommand('FormatBlock', false, 'h' + level);
			},
			onPostRender: function() {
				var control = this;
				ensureContainerClass(this);
				updateButtonState(editor, control, level);
				buttonControls.push({ control: control, level: level });
			}
		};

		if (icons && icons.getDataUri) {
			button.image = icons.getDataUri(level, icons.getBaseColor ? icons.getBaseColor() : null);
		} else {
			button.text = 'H' + level;
		}

		editor.addButton('h' + level, button);
	}

	tinymce.create('tinymce.plugins.WPHeadingButtons', {
		init : function(ed, url) {
			if (!enabledLevels.length) {
				return;
			}

			enabledLevels.forEach(function(level) {
				addHeadingButton(ed, level);
			});

			ed.on('NodeChange', function(e) {
				updateAllButtonStates(ed, e.element);
			});

		},

		getInfo : function() {
			return {
				longname : 'WP Heading Buttons',
				author : 'tercan',
				authorurl : 'http://tercan.net/',
				infourl : 'http://tercan.net/wp-heading-buttons/',
				version : "1.0"
			};
		}

	});
	tinymce.PluginManager.add('wpheadingbuttons', tinymce.plugins.WPHeadingButtons);
})();
