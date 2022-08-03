<?php

namespace Magenest\InstagramShop\Ui\Component\Listing\Columns;

class Link extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['url'] = '<a href="' . $item['url'] . '" target="_blank">' . $item['url'] . '</a>';
            }
        }
        return $dataSource;
    }
}
