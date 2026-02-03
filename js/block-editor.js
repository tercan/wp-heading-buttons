(function(wp) {
	if (!wp || !wp.hooks || !wp.element || !wp.compose || !wp.blocks || !wp.components || !wp.data) {
		return;
	}

	var editor = wp.blockEditor || wp.editor;
	if (!editor || !editor.BlockControls) {
		return;
	}

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var BlockControls = editor.BlockControls;
	var ToolbarGroup = wp.components.ToolbarGroup;
	var ToolbarButton = wp.components.ToolbarButton;
	var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
	var createBlock = wp.blocks.createBlock;
	var dispatch = wp.data.dispatch;
	var icons = window.wphbHeadingIcons || null;
	var settings = window.wphbHeadingButtonsSettings || {};
	var i18n = window.wphbHeadingButtonsI18n || {};

	var LEVELS = [1, 2, 3, 4, 5, 6];

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

	var enabledLevels = Array.isArray(settings.levels) ? normalizeLevels(settings.levels) : LEVELS.slice();

	function getHeadingLabel(level) {
		if (i18n.headingLabels && i18n.headingLabels[level]) {
			return i18n.headingLabels[level];
		}
		return 'Heading ' + level;
	}

	var groupLabel = i18n.headingLevels || 'Heading Levels';

	function getHeadingIcon(level) {
		if (icons && icons.getElement) {
			return icons.getElement(level, el, 'currentColor');
		}

		return el('span', { className: 'wphb-icon-fallback' }, 'H' + level);
	}

	var withHeadingButtons = createHigherOrderComponent(function(BlockEdit) {
		return function(props) {
			var isHeading = props.name === 'core/heading';
			var isParagraph = props.name === 'core/paragraph';

			if (!isHeading && !isParagraph) {
				return el(BlockEdit, props);
			}

			if (!enabledLevels.length) {
				return el(BlockEdit, props);
			}

			var attributes = props.attributes || {};
			var setAttributes = props.setAttributes;
			var clientId = props.clientId;

			function applyLevel(level) {
				if (isHeading) {
					setAttributes({ level: level });
					return;
				}

				var newAttributes = {
					level: level,
					content: attributes.content || ''
				};

				if (attributes.align) {
					newAttributes.align = attributes.align;
				}
				if (attributes.className) {
					newAttributes.className = attributes.className;
				}
				if (attributes.style) {
					newAttributes.style = attributes.style;
				}
				if (attributes.anchor) {
					newAttributes.anchor = attributes.anchor;
				}

				dispatch('core/block-editor').replaceBlock(
					clientId,
					createBlock('core/heading', newAttributes)
				);
			}

			return el(
				Fragment,
				null,
				el(BlockEdit, props),
				el(
					BlockControls,
					null,
					el(
						ToolbarGroup,
						{ label: groupLabel, className: 'wphb-container' },
						enabledLevels.map(function(level) {
							return el(ToolbarButton, {
								key: 'wphb-h' + level,
								icon: getHeadingIcon(level),
								label: getHeadingLabel(level),
								isPressed: isHeading && attributes.level === level,
								onClick: function() {
									applyLevel(level);
								}
							});
						})
					)
				)
			);
		};
	}, 'withHeadingButtons');

	wp.hooks.addFilter('editor.BlockEdit', 'wphb/heading-buttons', withHeadingButtons);
})(window.wp);
