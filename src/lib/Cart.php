<?php

namespace Fontebasso\Cart;

use Fontebasso\Core\Controller;

class Cart extends Controller
{

    public function get($cart_id)
    {
        $this->db->select('*');
        $this->db->from('carts');
        $this->db->where('id', $cart_id);
        return $this->db->get()->row();
    }
    
    public function getItems($cart_id)
    {
        $this->db->select('*');
        $this->db->from('cart_items');
        $this->db->where('cart_id', $cart_id);
        return $this->db->get()->result();
    }
    
    public function getCustomerCarts($customer_id)
    {
        $this->db->select('*');
        $this->db->from('carts');
        $this->db->where('customer_id', $customer_id);
        return $this->db->get()->result();
    }
    
    public function getAll()
    {
        $this->db->select('*');
        $this->db->from('carts');
        return $this->db->get()->result();
    }
    
    public function add($customer_id)
    {
        $data = [
            'customer_id' => $customer_id,
            'price' => '0.00',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->insert('carts', $data);
    }
    
    public function edit($cart_id, $data)
    {
        $this->db->where('id', $cart_id);
        return $this->db->update('carts', $data);
    }

}
