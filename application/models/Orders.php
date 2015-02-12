<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code) {
         $existing = $this->db->get_where('orderitems', array('item' => $code, 'order' => $num))->row();
        if ($existing != null) {
            $existing->quantity += 1;
            $this->orderitems->update($existing);
        } else {
            $newItem = $this->orderitems->create();
            $newItem->item = $code;
            $newItem->order = $num;
            $newItem->quantity = 1;
            $this->orderitems->add($newItem);
        }
    }

    // calculate the total for an order
    function total($num) {
        $total = 0;
        $items = $this->orderitems->some('order', $num);
        if(!is_null($items)){
            foreach($items as $item){
                $total += $this->menu->get($item->item)->price * $item->quantity;
            }
        }
        return $total;
    }

    // retrieve the details for an order
    function details($num) {
        return $this->orderitems->some('order',$num);
    }

    // cancel an order
    function flush($num) {
        $this->orderitems->delete_some($order_num);
        $record = $this->orders->get($order_num);
        $record->status = 'x';
        $this->orders->update($record);
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        $CI = & get_instance();
        $items = $CI->orderitems->group($num);
        $get = [];
        if(count($items) > 0) {
            foreach( $items as $item ){
                $menu = $CI->menu->get($item->item);
                $gotem[$menu->category] = 1;
            }
        return isset($get['m']) && isset($get['d']) && isset($get['s']);
        }
    }
}
