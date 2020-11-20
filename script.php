<?php
require __DIR__ .'/vendor/autoload.php';

$client = new \RetailCrm\ApiClient(
    'url',
    'key',
    \RetailCrm\ApiClient::V5
);

logger('request.log',$_REQUEST);

$_GET['id'] = 3141;
$order = $client->request->ordersGet($_GET['id'],'id','');
$height = 0; //Determinate max value
$width = 0; //Sum of values
$length = 0; //Sum of values

foreach ($order['order']['items'] as $item) {
    $product = $client->request->storeProducts([
        'offerIds' => [
            $item['offer']['id']
        ]
    ],1,100);
    foreach ($product['products'][0]['offers'] as $offer) {
        if ($offer['id'] != $item['offer']['id'])
            continue;
        $width += $offer['properties']['width'];
        $length += $offer['properties']['length'];
        if ($offer['properties']['height'] > $height)
            $height = $offer['properties']['height'];
    }
}

$orderEditAction = $client->request->ordersEdit([
    'id' => $order['order']['id'],
    'width' => $width,
    'length' => $length,
    'height' => $height

],'id',$order['order']['site']);

logger('orderEdit.log',$orderEditAction);

function logger($filename,$data = array()) {
    $fd = fopen(__DIR__ .'/logs/' . $filename, 'a');
    fwrite($fd, '[' . date('Y-m-d H:i:s') . '] => ' . print_r($data,true) . "\n");
    fclose($fd);
}