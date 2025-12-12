/**
 * Featured Success Stories Gutenberg Block
 * Version: 1.0.0
 */

(function (blocks, element, blockEditor, components, i18n, serverSideRender) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var RangeControl = components.RangeControl;
    var SelectControl = components.SelectControl;
    var ServerSideRender = serverSideRender;
    var __ = i18n.__;

    registerBlockType('business-showcase/success-stories', {
        title: __('Featured Success Stories', 'business-showcase-networking-hub'),
        description: __('Display top-rated business profiles as success stories', 'business-showcase-networking-hub'),
        icon: 'star-filled',
        category: 'business-showcase',
        attributes: {
            numberOfItems: {
                type: 'number',
                default: 6
            },
            orderBy: {
                type: 'string',
                default: 'rating'
            },
            layoutStyle: {
                type: 'string',
                default: 'grid'
            },
            columns: {
                type: 'number',
                default: 3
            },
            showRating: {
                type: 'boolean',
                default: true
            },
            showReviewCount: {
                type: 'boolean',
                default: true
            }
        },

        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, {
                        title: __('Display Settings', 'business-showcase-networking-hub'),
                        initialOpen: true
                    },
                        el(RangeControl, {
                            label: __('Number of Items', 'business-showcase-networking-hub'),
                            value: attributes.numberOfItems,
                            onChange: function (value) {
                                setAttributes({ numberOfItems: value });
                            },
                            min: 1,
                            max: 20
                        }),
                        el(SelectControl, {
                            label: __('Order By', 'business-showcase-networking-hub'),
                            value: attributes.orderBy,
                            options: [
                                { label: __('Highest Rating', 'business-showcase-networking-hub'), value: 'rating' },
                                { label: __('Most Reviews', 'business-showcase-networking-hub'), value: 'review_count' },
                                { label: __('Recent', 'business-showcase-networking-hub'), value: 'date' }
                            ],
                            onChange: function (value) {
                                setAttributes({ orderBy: value });
                            }
                        })
                    ),
                    el(PanelBody, {
                        title: __('Layout Settings', 'business-showcase-networking-hub'),
                        initialOpen: true
                    },
                        el(SelectControl, {
                            label: __('Layout Style', 'business-showcase-networking-hub'),
                            value: attributes.layoutStyle,
                            options: [
                                { label: __('Grid', 'business-showcase-networking-hub'), value: 'grid' },
                                { label: __('List', 'business-showcase-networking-hub'), value: 'list' },
                                { label: __('Cards', 'business-showcase-networking-hub'), value: 'cards' }
                            ],
                            onChange: function (value) {
                                setAttributes({ layoutStyle: value });
                            }
                        }),
                        attributes.layoutStyle === 'grid' || attributes.layoutStyle === 'cards' ? 
                            el(RangeControl, {
                                label: __('Columns', 'business-showcase-networking-hub'),
                                value: attributes.columns,
                                onChange: function (value) {
                                    setAttributes({ columns: value });
                                },
                                min: 1,
                                max: 4
                            }) : null
                    )
                ),
                el('div', { className: props.className, key: 'preview' },
                    el(ServerSideRender, {
                        block: 'business-showcase/success-stories',
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
