import { __ } from '@wordpress/i18n';

export const themes = [
	{ label: __('Default', 'icon-list'), value: 'default' },
	{ label: __('Theme 1', 'icon-list'), value: 'theme1' },
	{ label: __('Theme 2', 'icon-list'), value: 'theme2' }
];

export const aligns = [
	{ label: __('Left', 'icon-list'), value: 'start', icon: 'editor-alignleft' },
	{ label: __('Center', 'icon-list'), value: 'center', icon: 'editor-aligncenter' },
	{ label: __('Right', 'icon-list'), value: 'end', icon: 'editor-alignright' }
];

export const generalStyleTabs = [
	{ name: 'general', title: __('General', 'icon-list') },
	{ name: 'style', title: __('Style', 'icon-list') }
];