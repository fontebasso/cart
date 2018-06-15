<?php

namespace Fontebasso\Cart;

use Fontebasso\Core\Controller;

class Item extends Controller {
    
    public function get($item_id)
    {
        $this->db->select('*');
        $this->db->from('cart_items');
        $this->db->where('id', $item_id);
        return $this->db->get()->row();
    }
    
    public function add($data)
    {
        return $this->db->insert('cart_items', $data);
    }
    
    public function addOffer($data)
    {
        return $this->db->insert('cart_item_offers', $data);
    }
    
    public function deleteProduct($product_id, $cart_id)
    {
        $this->db->select('id');
        $this->db->from('cart_items');
        $this->db->where('product_id', $product_id);
        $this->db->where('cart_id', $cart_id);
        $cart_item = $this->db->get()->row();
        $cart_item_id = $cart_item['id'];
        
        $this->db->where('id', $cart_item_id);
        $this->db->delete('cart_items');

        if ($cart_item_id) {
            $this->db->where('cart_item_id', $cart_item_id);
            $this->db->delete('cart_item_offers');
        }
    }
    
    public function getOffers($cart_item_id)
    {
        $this->db->select('*');
        $this->db->from('cart_item_offers');
        $this->db->where('cart_item_id', $cart_item_id);
        return $this->db->get()->result();
    }
    
}
