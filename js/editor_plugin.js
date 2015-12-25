(function() {

	tinymce.PluginManager.requireLangPack('wpheadingbuttons', 'tr_TR,ar_SA,de_DE,fr_FR,it_IT');

	tinymce.create('tinymce.plugins.WPHeadingButtons', {
		init : function(ed, url) {
			ed.addButton('h1', {
				title : 'Heading 1',
				icon: 'icon wphb-ico btn-h1',
				onclick : function() {
					ed.execCommand('FormatBlock', false, 'h1');
				}
			});
			ed.addButton('h2', {
				title : 'Heading 2',
				icon: 'icon wphb-ico btn-h2',
				onclick : function() {
					ed.execCommand('FormatBlock', false, 'h2');
				}
			});
			ed.addButton('h3', {
				title : 'Heading 3',
				icon: 'icon wphb-ico btn-h3',
				onclick : function() {
					ed.execCommand('FormatBlock', false, 'h3');
				}
			});
			ed.addButton('h4', {
				title : 'Heading 4',
				icon: 'icon wphb-ico btn-h4',
				onclick : function() {
					ed.execCommand('FormatBlock', false, 'h4');
				}
			});
			ed.addButton('h5', {
				title : 'Heading 5',
				icon: 'icon wphb-ico btn-h5',
				onclick : function() {
					ed.execCommand('FormatBlock', false, 'h5');
				}
			});
			ed.addButton('h6', {
				title : 'Heading 6',
				icon: 'icon wphb-ico btn-h6',
				onclick : function() {
					ed.execCommand('FormatBlock', false, 'h6');
				}
			});

		},

		getInfo : function() {
			return {
				longname : 'WP Heading Buttons',
				author : 'tercan',
				authorurl : 'http://tercan.net/',
				infourl : 'http://tercan.net/',
				version : "0.3"
			};
		}

	});
	tinymce.PluginManager.add('wpheadingbuttons', tinymce.plugins.WPHeadingButtons);
})();