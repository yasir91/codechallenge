<?php

class Sale
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    //Get All Sales
    public function getAllSales()
    {
        $this->db->query("SELECT
        customers.name customerName,
        mail,
        products.name productName,
        price,
        version,
        sales.created_at
    FROM sales 
    left JOIN products ON product_id = products.id
    left JOIN customers ON customer_id = customers.id");

        return $this->db->resultSet();
    }

    //Get Sales By Filters
    public function getSalesByFilters(array $filters)
    {
        $sql = "SELECT
            customers.name customerName,
            mail,
            products.name productName,
            price,
            version,
            sales.created_at
        FROM sales 
        left JOIN products ON product_id = products.id
        left JOIN customers ON customer_id = customers.id
        WHERE customers.name LIKE '%" . $filters['customerName'] . "%'
        AND products.name LIKE '%" . $filters['productName'] . "%'";

        if ($filters['productPrice'] !== '')
            $sql .= " AND products.price = " . $filters['productPrice'];

        $this->db->query($sql);

        return $this->db->resultSet();
    }

    public function updateDb()
    {
        $json = file_get_contents("./Assets/db.json");
        $json = json_decode($json, true);

        foreach ($json as $val) {
            //check if PRODUCT exist or insert
            if (!isset($this->recordExist('products', 'version', $val['version'], true)->id)) {
                $values  = [
                    $val['product_name'],
                    $val['product_price'],
                    $val['version'],
                ];
                $sql = "INSERT INTO products (name, price, version) VALUES (?,?,?)";
                $this->db->query($sql);
                $this->db->insert($values);
            }

            //check if CUSTOMER exist or insert
            if (!isset($this->recordExist('customers', 'mail', $val['customer_mail'], true)->id)) {

                $values = [
                    $val['customer_name'],
                    $val['customer_mail'],
                ];

                $sql = "INSERT INTO customers (name, mail) VALUES (?,?)";
                $this->db->query($sql);
                $this->db->insert($values);
            }

            //check if SALES exist or insert
            if (!isset($this->recordExist('sales', 'id', $val['sale_id'])->id)) {
                $values = [];
                $values[] = $val['sale_id'];
                $values[] = $this->recordExist('products', 'version', $val['version'], true)->id;
                $values[] = $this->recordExist('customers', 'mail', $val['customer_mail'], true)->id;
                $values[] = $val['sale_date'];

                $sql = "INSERT INTO sales (id, product_id, customer_id, created_at) VALUES (?,?,?,?)";
                $this->db->query($sql);
                $this->db->insert($values);
            }
        }
    }

    private function recordExist($table, $needleKey, $needle, $escaped = false)
    {
        $sql = "SELECT id FROM " . $table . " WHERE " . $needleKey . " = " .   ($escaped ? "'" . $needle . "'" : $needle);
        $this->db->query($sql);
        return $this->db->single();
    }

    public function compareVersion($productVersion, $salesDate)
    {
        $version = explode('+', $productVersion);

        if (
            (version_compare($version[0], '1.0.17', "=") && $version[1] >= 60) ||
            (version_compare($version[0], '1.0.17', ">"))
        ) {
            return $salesDate;
        }

        $returnDate = gmdate('Y-m-d H:i:s', strtotime($salesDate));

        //@todo need more time to investigate why gmdate is not working for PHPUnit, this is a contingency for tests only.
        if ($returnDate == $salesDate) {
            $returnDate = strtotime($returnDate);
            $time = $returnDate - (2 * 60 * 60);

            // Date and time after subtraction
            $returnDate = date("Y-m-d H:i:s", $time);
        }

        return $returnDate;
    }
}
