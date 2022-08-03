<?php
namespace Magenest\InstagramShop\Block\Adminhtml\TaggedPhoto;

use Magento\Backend\Block\Widget\Grid as WidgetGrid;

/**
 * Class Grid
 * @package Magenest\InstagramShop\Block\Adminhtml\TaggedPhoto
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magenest\InstagramShop\Model\TaggedPhotoFactory
     */
    protected $_photoFactory;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magenest\InstagramShop\Model\TaggedPhotoFactory $photoFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magenest\InstagramShop\Model\TaggedPhotoFactory $photoFactory,
        array $data = []
    ) {
        $this->_photoFactory = $photoFactory;
        $this->setEmptyText(__('No Photos Found'));
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize the collection
     *
     * @return WidgetGrid
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_photoFactory->create()->getCollection());

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header'=> __('ID'),
                'index' => 'id',
            ]
        );
        $this->addColumn(
            'source',
            [
                'header'=> __('Source'),
                'index' => 'source',
                'renderer' => '\Magenest\InstagramShop\Block\Adminhtml\TaggedPhoto\Renderer\Image'
            ]
        );
        $this->addColumn(
            'url',
            [
                'header'=> __('URL'),
                'index' => 'url',
                'renderer' => '\Magenest\InstagramShop\Block\Adminhtml\TaggedPhoto\Renderer\Link'
            ]
        );
        $this->addColumn(
            'tag_name',
            [
                'header'=> __('Tag Name'),
                'index' => 'tag_name',
            ]
        );
        $this->addColumn(
            'user',
            [
                'header'=> __('User Name'),
                'index' => 'user',
            ]
        );
        $this->addColumn(
            'photo_id',
            [
                'header'=> __('Photo Id on Instagram'),
                'index' => 'photo_id',
            ]
        );
        $this->addColumn(
            'likes',
            [
                'header'=> __('Likes'),
                'index' => 'likes',
            ]
        );
        $this->addColumn(
            'comments',
            [
                'header'=> __('Comments'),
                'index' => 'comments',
            ]
        );
        $this->addColumn(
            'caption',
            [
                'header'=> __('Caption'),
                'index' => 'caption',
            ]
        );

        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');
        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label'      => __('Delete'),
                'url'        => $this->getUrl('*/*/massdelete'),
                'confirm' => 'Are you sure?'
            )
        );

        return $this;
    }
}
