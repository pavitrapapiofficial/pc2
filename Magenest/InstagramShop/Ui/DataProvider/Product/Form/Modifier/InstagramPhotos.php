<?php

namespace Magenest\InstagramShop\Ui\DataProvider\Product\Form\Modifier;

use Magenest\InstagramShop\Model\PhotoFactory;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Modal;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;

class InstagramPhotos extends AbstractModifier
{
    const GROUP_MAGENEST_INSTAGRAM_PHOTOS = 'magenest_instagram_photos';
    const GROUP_MAGENEST_INSTAGRAM_PHOTOS_GRID = 'magenest_instagram_photos_grid';
    const INSTAGRAM_PHOTOS_LISTING = 'magenest_instagram_photos_listing';
    const INSTAGRAM_PHOTOS_MODAL = 'magenest_instagram_photos_modal';
    const LINK_TYPE = 'instagram_photos';
    const INSTAGRAM_PHOTOS_ATTRIBUTE_CODE = 'instagram_photos';
    const SPINNER = 'instagram_photos_columns';

    protected $urlBuilder;
    protected $locator;
    protected $photoFactory;
    protected $featureResource;

    public function __construct(
        UrlInterface $urlBuilder,
        LocatorInterface $locator,
        PhotoFactory $photoFactory
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->locator = $locator;
        $this->photoFactory = $photoFactory;
    }

    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $data[$product->getId()]['links'][self::LINK_TYPE] = [];
        if ($ids = $product->getData(self::INSTAGRAM_PHOTOS_ATTRIBUTE_CODE)) {
            $ids = explode(',', $ids);
            $data = [];
            foreach ($ids as $id) {
                $photo = $this->photoFactory->create()->load($id);
                if ($photo->getId()) {
                    $data[] = $photo->getData();
                }
            }
            $data[$product->getId()]['links'][self::LINK_TYPE] = $data;
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                self::GROUP_MAGENEST_INSTAGRAM_PHOTOS => [
                    'children' => $this->getChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Instagram Photos'),
                                'collapsible' => true,
                                'opened' => false,
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $meta,
                                    'content',
                                    0
                                ),
                                'additionalClasses' => 'instagram-tab'
                            ],
                        ],
                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * Retrieve child meta configuration
     *
     * @return array
     */
    protected function getChildren()
    {
        $children = [
            'product_features_button_set' => $this->getButtonSet(),
            self::INSTAGRAM_PHOTOS_MODAL => $this->getModal(),
            self::LINK_TYPE => $this->getGrid()
        ];
        return $children;
    }

    /**
     * Returns Modal configuration
     *
     * @return array
     */
    protected function getModal()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'provider' =>
                            'product_form.product_form_data_source',
                        'options' => [
                            'title' => __('Instagram Photos'),
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => ['closeModal']
                                ],
                                [
                                    'text' => __('Add Photos'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . self::INSTAGRAM_PHOTOS_LISTING,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [self::INSTAGRAM_PHOTOS_LISTING => $this->getListing()],
        ];
    }

    /**
     * Returns Listing configuration
     *
     * @return array
     */
    protected function getListing()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => false,
                        'componentType' => 'insertListing',
                        'dataScope' => self::INSTAGRAM_PHOTOS_LISTING,
                        'externalProvider' =>
                            self::INSTAGRAM_PHOTOS_LISTING
                            . '.'
                            . self::INSTAGRAM_PHOTOS_LISTING
                            . '_data_source',
                        'selectionsProvider' =>
                            self::INSTAGRAM_PHOTOS_LISTING
                            . '.'
                            . self::INSTAGRAM_PHOTOS_LISTING
                            . '.' . self::SPINNER . '.ids',
                        'ns' => self::INSTAGRAM_PHOTOS_LISTING,
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                        'realTimeLink' => true,
                        'provider' =>
                            'product_form'
                            . '.'
                            . 'product_form'
                            . '_data_source',
                        'dataLinks' => ['imports' => false, 'exports' => false],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns Buttons Set configuration
     *
     * @return array
     */
    protected function getButtonSet()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => __('This section allows you to add Instagram photos to product'),
                        'template' => 'ui/form/components/complex',
                        'additionalClasses' => 'instagram-tab-body'
                    ],
                ],
            ],
            'children' => [
                'add_photos_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' =>
                                            'product_form' . '.' . 'product_form'
                                            . '.'
                                            . self::GROUP_MAGENEST_INSTAGRAM_PHOTOS
                                            . '.'
                                            . self::INSTAGRAM_PHOTOS_MODAL,
                                        'actionName' => 'openModal',
                                    ],
                                    [
                                        'targetName' =>
                                            'product_form' . '.' . 'product_form'
                                            . '.'
                                            . self::GROUP_MAGENEST_INSTAGRAM_PHOTOS
                                            . '.'
                                            . self::INSTAGRAM_PHOTOS_MODAL
                                            . '.'
                                            . self::INSTAGRAM_PHOTOS_LISTING,
                                        'actionName' => 'render',
                                    ],
                                ],
                                'title' => __('Add Photos to Product'),
                                'sortOrder' => 20,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns dynamic rows configuration
     *
     * @return array
     */
    protected function getGrid()
    {
        $grid = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'itemTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => self::INSTAGRAM_PHOTOS_LISTING,
                        'map' => [
                            'id' => 'id',
                            'source' => 'source',
//                            'url' => 'url',
                            'caption' => 'caption',
                            'likes' => 'likes',
                            'comments' => 'comments',
//                            'photo_id' => 'photo_id'
                        ],
                        'links' => ['insertData' => '${ $.provider }:${ $.dataProvider }'],
                        'sortOrder' => 20,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                    ],
                ],
            ],
            'children' => $this->getRows(),
        ];
        return $grid;
    }

    /**
     * Returns Dynamic rows records configuration
     *
     * @return array
     */
    protected function getRows()
    {
        return [
            'record' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'container',
                            'isTemplate' => true,
                            'is_collection' => true,
                            'component' => 'Magento_Ui/js/dynamic-rows/record',
                            'dataScope' => '',
                        ],
                    ],
                ],
                'children' => $this->fillMeta(),
            ],
        ];
    }

    /**
     * Fill meta columns
     *
     * @return array
     */
    protected function fillMeta()
    {
        return [
            'id' => $this->getTextColumn('id', true, __('ID'), 10),
            'source' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Form\Field::NAME,
                            'formElement' => Form\Element\Input::NAME,
                            'elementTmpl' => 'ui/dynamic-rows/cells/thumbnail',
                            'dataType' => Form\Element\DataType\Text::NAME,
                            'dataScope' => 'source',
                            'fit' => true,
                            'label' => __('Image'),
                            'sortOrder' => 15,
                        ],
                    ],
                ],
            ],
            'caption' => $this->getTextColumn('caption', false, __('Caption'), 30),
            'likes' => $this->getTextColumn('likes', false, __('Likes'), 40),
            'comments' => $this->getTextColumn('comments', false, __('Comments'), 50),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Form\Element\DataType\Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 90,
                            'fit' => true,
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * Returns text column configuration for the dynamic grid
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    protected function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Form\Field::NAME,
                        'formElement' => Form\Element\Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'dataType' => Form\Element\DataType\Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder
                    ],
                ],
            ],
        ];
        return $column;
    }
}
