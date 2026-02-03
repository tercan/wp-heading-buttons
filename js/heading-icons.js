(function(root) {
	var DEFAULT_COLOR = '#50575e';
	var ICON_SIZE = 20;
	var TEXT_Y = 13;
	var TEXT_SIZE = 9;
	var TEXT_WEIGHT = '700';
	var TEXT_FONT = 'sans-serif';

	function getSvg(level, color) {
		var iconColor = color || DEFAULT_COLOR;
		return ''
			+ '<svg xmlns="http://www.w3.org/2000/svg" width="' + ICON_SIZE + '" height="' + ICON_SIZE + '" viewBox="0 0 20 20" aria-hidden="true" focusable="false">'
			+ '<rect x="1" y="1" width="18" height="18" rx="2" ry="2" fill="none" stroke="' + iconColor + '" stroke-width="1"/>'
			+ '<text x="10" y="' + TEXT_Y + '" text-anchor="middle" font-size="' + TEXT_SIZE + '" font-family="' + TEXT_FONT + '" font-weight="' + TEXT_WEIGHT + '" fill="' + iconColor + '">H'
			+ level
			+ '</text></svg>';
	}

	function getDataUri(level, color) {
		return 'data:image/svg+xml;utf8,' + encodeURIComponent(getSvg(level, color));
	}

	function getElement(level, createElement, color) {
		if (!createElement) {
			return null;
		}

		return createElement('span', {
			className: 'wphb-svg-icon',
			dangerouslySetInnerHTML: { __html: getSvg(level, color) }
		});
	}

	root.wphbHeadingIcons = {
		getSvg: getSvg,
		getDataUri: getDataUri,
		getElement: getElement,
		getBaseColor: function() {
			return DEFAULT_COLOR;
		}
	};
})(window);
