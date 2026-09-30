<?php
session_start();

class Cart {
    protected array $cart_contents = [];

    public function __construct(){
        $this->cart_contents = !empty($_SESSION['cart_contents'])
            ? (array)$_SESSION['cart_contents']
            : ['cart_total' => 0.0, 'total_items' => 0];
    }

    public function contents(): array {
        $cart = array_reverse($this->cart_contents, true);
        unset($cart['total_items'], $cart['cart_total']);
        return $cart;
    }

    public function get_item(string $row_id){
        return (in_array($row_id, ['total_items','cart_total'], true) || !isset($this->cart_contents[$row_id]))
            ? false
            : $this->cart_contents[$row_id];
    }

    public function total_items(): int {
        return (int)$this->cart_contents['total_items'];
    }

    public function total(): float {
        return (float)$this->cart_contents['cart_total'];
    }

    public function insert(array $item = []){
        if (empty($item) || !isset($item['id'], $item['price'], $item['qty'])) return false;

        $qty   = filter_var($item['qty'],   FILTER_VALIDATE_INT,   ['options'=>['min_range'=>1]]);
        $price = filter_var($item['price'], FILTER_VALIDATE_FLOAT);

        if ($qty === false || $price === false) return false;

        $rowid = md5((string)$item['id']);
        $item['rowid'] = $rowid;
        $item['qty']   = (int)$qty;
        $item['price'] = (float)$price;

        $this->cart_contents[$rowid] = $item;

        return $this->save_cart() ? $rowid : false;
    }

    public function update(array $item = []): bool {
        if (empty($item) || !isset($item['rowid'], $this->cart_contents[$item['rowid']])) return false;

        $rowid = $item['rowid'];

        if (isset($item['qty'])) {
            $qty = filter_var($item['qty'], FILTER_VALIDATE_INT, ['options'=>['min_range'=>0]]);
            if ($qty === false) return false;
            if ($qty === 0) { unset($this->cart_contents[$rowid]); return $this->save_cart(); }
            $this->cart_contents[$rowid]['qty'] = (int)$qty;
        }

        if (isset($item['price'])) {
            $price = filter_var($item['price'], FILTER_VALIDATE_FLOAT);
            if ($price === false) return false;
            $this->cart_contents[$rowid]['price'] = (float)$price;
        }

        foreach ($item as $k=>$v) {
            if (!in_array($k, ['id','name','rowid','qty','price'], true)) {
                $this->cart_contents[$rowid][$k] = $v;
            }
        }

        return $this->save_cart();
    }

    protected function save_cart(): bool {
        $this->cart_contents['total_items'] = 0;
        $this->cart_contents['cart_total']  = 0.0;

        foreach ($this->cart_contents as $key => $val) {
            if (!is_array($val) || !isset($val['price'], $val['qty'])) continue;

            $qty   = (int)$val['qty'];
            $price = (float)$val['price'];

            $this->cart_contents['total_items'] += $qty;
            $this->cart_contents['cart_total']  += $price * $qty;
            $this->cart_contents[$key]['subtotal'] = $price * $qty;
        }

        if (count($this->cart_contents) <= 2) {
            unset($_SESSION['cart_contents']);
            return false;
        }

        $_SESSION['cart_contents'] = $this->cart_contents;
        return true;
    }

    public function remove(string $row_id): bool {
        unset($this->cart_contents[$row_id]);
        return $this->save_cart();
    }

    public function destroy(): void {
        $this->cart_contents = ['cart_total' => 0.0, 'total_items' => 0];
        unset($_SESSION['cart_contents']);
    }
}
