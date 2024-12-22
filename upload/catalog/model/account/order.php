<?php
namespace Opencart\Catalog\Model\Account;
/**
 * Class Order
 *
 * Can be called from $this->load->model('account/order');
 *
 * @package Opencart\Catalog\Model\Account
 */
class Order extends \Opencart\System\Engine\Model {
	/**
	 * Get Order
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return array<string, mixed> order record that has order ID
	 */
	public function getOrder(int $order_id): array {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `order_id` = '" . (int)$order_id . "' AND `customer_id` = '" . (int)$this->customer->getId() . "' AND `customer_id` != '0' AND `order_status_id` > '0'");

		if ($order_query->num_rows) {
			// Country
			$this->load->model('localisation/country');

			$country_info = $this->model_localisation_country->getCountry($order_query->row['payment_country_id']);

			if ($country_info) {
				$payment_iso_code_2 = $country_info['iso_code_2'];
				$payment_iso_code_3 = $country_info['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			// Zone
			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($order_query->row['payment_zone_id']);

			if ($zone_info) {
				$payment_zone_code = $zone_info['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_info = $this->model_localisation_country->getCountry($order_query->row['shipping_country_id']);

			if ($country_info) {
				$shipping_iso_code_2 = $country_info['iso_code_2'];
				$shipping_iso_code_3 = $country_info['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			// Zone
			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($order_query->row['shipping_zone_id']);

			if ($zone_info) {
				$shipping_zone_code = $zone_info['code'];
			} else {
				$shipping_zone_code = '';
			}

			return [
				'payment_zone_code'   => $payment_zone_code,
				'payment_iso_code_2'  => $payment_iso_code_2,
				'payment_iso_code_3'  => $payment_iso_code_3,
				'payment_method'      => $order_query->row['payment_method'] ? json_decode($order_query->row['payment_method'], true) : '',
				'shipping_zone_code'  => $shipping_zone_code,
				'shipping_iso_code_2' => $shipping_iso_code_2,
				'shipping_iso_code_3' => $shipping_iso_code_3,
				'shipping_method'     => $order_query->row['shipping_method'] ? json_decode($order_query->row['shipping_method'], true) : ''
			] + $order_query->row;
		} else {
			return [];
		}
	}

	/**
	 * Get Orders
	 *
	 * @param int $start
	 * @param int $limit
	 *
	 * @return array<int, array<string, mixed>> order records
	 */
	public function getOrders(int $start = 0, int $limit = 20): array {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `customer_id` = '" . (int)$this->customer->getId() . "' AND `order_status_id` > '0' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' ORDER BY `order_id` DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	/**
	 * Get Orders By Subscription ID
	 *
	 * @param int $subscription_id primary key of the subscription record
	 * @param int $start
	 * @param int $limit
	 *
	 * @return array<int, array<string, mixed>> order records that have subscription ID
	 */
	public function getOrdersBySubscriptionId(int $subscription_id, int $start = 0, int $limit = 20): array {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `subscription_id` = '" . (int)$subscription_id . "' AND `customer_id` = '" . (int)$this->customer->getId() . "' AND `order_status_id` > '0' AND `store_id` = '" . (int)$this->config->get('config_store_id') . "' ORDER BY `order_id` DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	/**
	 * Get Product
	 *
	 * @param int $order_id         primary key of the order record
	 * @param int $order_product_id primary key of the order product record
	 *
	 * @return array<string, mixed> product record that have order ID, order product ID
	 */
	public function getProduct(int $order_id, int $order_product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int)$order_id . "' AND `order_product_id` = '" . (int)$order_product_id . "'");

		return $query->row;
	}

	/**
	 * Get Products
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return array<int, array<string, mixed>> product records that have order ID
	 */
	public function getProducts(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int)$order_id . "'");

		return $query->rows;
	}

	/**
	 * Get Options
	 *
	 * @param int $order_id         primary key of the order record
	 * @param int $order_product_id primary key of the order product record
	 *
	 * @return array<int, array<string, mixed>> option records that have order ID, order product ID
	 */
	public function getOptions(int $order_id, int $order_product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_option` WHERE `order_id` = '" . (int)$order_id . "' AND `order_product_id` = '" . (int)$order_product_id . "'");

		return $query->rows;
	}

	/**
	 * Get Subscription
	 *
	 * @param int $order_id         primary key of the order record
	 * @param int $order_product_id primary key of the order product record
	 *
	 * @return array<string, mixed> order subscription record that has order ID, order product ID
	 */
	public function getSubscription(int $order_id, int $order_product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_subscription` WHERE `order_id` = '" . (int)$order_id . "' AND `order_product_id` = '" . (int)$order_product_id . "'");

		return $query->row;
	}

	/**
	 * Get Totals
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return array<int, array<string, mixed>> total records that have order ID
	 */
	public function getTotals(int $order_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = '" . (int)$order_id . "' ORDER BY `sort_order`");

		return $query->rows;
	}

	/**
	 * Get Histories
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return array<int, array<string, mixed>> history records that have order ID
	 */
	public function getHistories(int $order_id): array {
		$query = $this->db->query("SELECT `date_added`, `os`.`name` AS `status`, `oh`.`comment`, `oh`.`notify` FROM `" . DB_PREFIX . "order_history` `oh` LEFT JOIN `" . DB_PREFIX . "order_status` `os` ON `oh`.`order_status_id` = `os`.`order_status_id` WHERE `oh`.`order_id` = '" . (int)$order_id . "' AND `os`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `oh`.`date_added`");

		return $query->rows;
	}

	/**
	 * Get Total Histories
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return int total number of history records that have order ID
	 */
	public function getTotalHistories(int $order_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order_history` WHERE `order_id` = '" . (int)$order_id . "'");

		if ($query->num_rows) {
			return (int)$query->row['total'];
		} else {
			return 0;
		}
	}

	/**
	 * Get Total Orders
	 *
	 * @return int total number of order records
	 */
	public function getTotalOrders(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order` `o` WHERE `customer_id` = '" . (int)$this->customer->getId() . "' AND `o`.`order_status_id` > '0' AND `o`.`store_id` = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['total'];
		} else {
			return 0;
		}
	}

	/**
	 * Get Total Orders By Product ID
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return int total number of order product records that have product ID
	 */
	public function getTotalOrdersByProductId(int $product_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order_product` `op` LEFT JOIN `" . DB_PREFIX . "order` `o` ON (`op`.`order_id` = `o`.`order_id`) WHERE `o`.`customer_id` = '" . (int)$this->customer->getId() . "' AND `op`.`product_id` = '" . (int)$product_id . "'");

		if ($query->num_rows) {
			return (int)$query->row['total'];
		} else {
			return 0;
		}
	}

	/**
	 * Get Total Products By Order ID
	 *
	 * @param int $order_id primary key of the order record
	 *
	 * @return int total number of product records that have order ID
	 */
	public function getTotalProductsByOrderId(int $order_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int)$order_id . "'");

		if ($query->num_rows) {
			return (int)$query->row['total'];
		} else {
			return 0;
		}
	}

	/**
	 * Get Total Orders By Subscription ID
	 *
	 * @param int $subscription_id primary key of the subscription record
	 *
	 * @return int total number of subscription records that have subscription ID
	 */
	public function getTotalOrdersBySubscriptionId(int $subscription_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order` WHERE `subscription_id` = '" . (int)$subscription_id . "' AND `customer_id` = '" . (int)$this->customer->getId() . "'");

		return (int)$query->row['total'];
	}
}
