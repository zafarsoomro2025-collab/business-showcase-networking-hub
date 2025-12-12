/**
 * Business Directory Gutenberg Block
 * Version: 1.0.0
 */

(function (blocks, element, blockEditor, components, i18n, serverSideRender) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var SelectControl = components.SelectControl;
    var RangeControl = components.RangeControl;
    var ToggleControl = components.ToggleControl;
    var ServerSideRender = serverSideRender;
    var __ = i18n.__;

    registerBlockType('business-showcase/directory', {
        title: __('Business Directory', 'business-showcase-networking-hub'),
        description: __('Display a grid of business profiles with filtering options', 'business-showcase-networking-hub'),
        icon: 'building',
        category: 'widgets',
        attributes: {
            postsPerPage: {
                type: 'number',
                default: 12
            },
            category: {
                type: 'string',
                default: ''
            },
            service: {
                type: 'string',
                default: ''
            },
            featuredOnly: {
                type: 'boolean',
                default: false
            },
            columns: {
                type: 'number',
                default: 3
            }
        },

        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            // Category options
            var categoryOptions = [
                { label: __('All Categories', 'business-showcase-networking-hub'), value: '' }
            ];
            
            if (businessShowcaseBlock.categories) {
                businessShowcaseBlock.categories.forEach(function (cat) {
                    categoryOptions.push({ label: cat.name, value: cat.slug });
                });
            }

            // Service options
            var serviceOptions = [
                { label: __('All Services', 'business-showcase-networking-hub'), value: '' }
            ];
            
            if (businessShowcaseBlock.services) {
                for (var key in businessShowcaseBlock.services) {
                    serviceOptions.push({ label: businessShowcaseBlock.services[key], value: key });
                }
            }

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, {
                        title: __('Display Settings', 'business-showcase-networking-hub'),
                        initialOpen: true
                    },
                        el(RangeControl, {
                            label: __('Number of Items', 'business-showcase-networking-hub'),
                            value: attributes.postsPerPage,
                            onChange: function (value) {
                                setAttributes({ postsPerPage: value });
                            },
                            min: 1,
                            max: 50
                        }),
                        el(RangeControl, {
                            label: __('Columns', 'business-showcase-networking-hub'),
                            value: attributes.columns,
                            onChange: function (value) {
                                setAttributes({ columns: value });
                            },
                            min: 1,
                            max: 4
                        }),
                        el(ToggleControl, {
                            label: __('Show Featured Only', 'business-showcase-networking-hub'),
                            checked: attributes.featuredOnly,
                            onChange: function (value) {
                                setAttributes({ featuredOnly: value });
                            }
                        })
                    ),
                    el(PanelBody, {
                        title: __('Filter Settings', 'business-showcase-networking-hub'),
                        initialOpen: true
                    },
                        el(SelectControl, {
                            label: __('Filter by Category', 'business-showcase-networking-hub'),
                            value: attributes.category,
                            options: categoryOptions,
                            onChange: function (value) {
                                setAttributes({ category: value });
                            }
                        }),
                        el(SelectControl, {
                            label: __('Filter by Service', 'business-showcase-networking-hub'),
                            value: attributes.service,
                            options: serviceOptions,
                            onChange: function (value) {
                                setAttributes({ service: value });
                            }
                        })
                    )
                ),
                el('div', { className: props.className, key: 'preview' },
                    el(ServerSideRender, {
                        block: 'business-showcase/directory',
                        attributes: attributes
                    })
                )
            ];
        },

        save: function () {
            // Server-side rendering
            return null;
        }
    });

})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n,
    window.wp.serverSideRender
);
