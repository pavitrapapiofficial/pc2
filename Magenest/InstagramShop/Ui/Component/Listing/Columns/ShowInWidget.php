<?php

namespace Magenest\InstagramShop\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class ShowInWidget extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item[$fieldName] == 1) {
                    $item[$fieldName] = __('Show');
                } else if ($item[$fieldName] == 0) {
                    $item[$fieldName] = __('Hidden');
                }
            }
        }
        return $dataSource;
    }
}
